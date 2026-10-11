<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\ChatbotSession;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\ShippingDistrict;
use App\Models\SmsGateway;
use App\Models\Thana;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $request->validate([
            'channel'   => 'required|string|in:whatsapp,messenger',
            'sender_id' => 'required|string',
            'message'   => 'required|string',
        ]);

        $session = ChatbotSession::firstOrCreate(
            [
                'channel'   => $request->channel,
                'sender_id' => $request->sender_id,
            ],
            [
                'state' => 'start',
                'cart'  => [],
            ]
        );

        $message = trim($request->message);

        // প্রথম conversation
        if ($session->state === 'start') {
            $session->update([
                'state' => 'product_search',
            ]);

            return response()->json([
                'status'  => true,
                'type'    => 'text',
                'message' => "আসসালামু আলাইকুম 👋\n\nSmart Bangla Shop-এ স্বাগতম। ❤️\n\nআপনি কী পণ্য খুঁজছেন?",
            ]);
        }

        return response()->json([
            'status'  => true,
            'type'    => 'text',
            'message' => 'আপনি কোন পণ্যটি খুঁজছেন? পণ্যের নাম লিখে জানান।',
        ]);
    }

    public function productSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
        ]);

        $search = trim($request->input('query'));

        $stopWords = [
            'i', 'need', 'want', 'give', 'please', 'show', 'have', 'available', 
            'product', 'products', 'do', 'you', 'any', 'is', 'there', 'আমি', 
            'আমার', 'আমাকে', 'চাই', 'লাগবে', 'দেন', 'দিন', 'দেখান', 'আছে', 
            'কি', 'কোন', 'পণ্য', 'প্রোডাক্ট', 'ভাই', 'আপনাদের', 'কাছে', 'দাম'
        ];

        // Safe multibyte split for Bangla and English lowercasing
        $words = preg_split('/\s+/', mb_strtolower($search, 'UTF-8'));

        $words = array_filter($words, function ($word) use ($stopWords) {
            $word = trim($word);

            return $word !== ''
                && ! in_array($word, $stopWords)
                && mb_strlen($word, 'UTF-8') >= 2;
        });

        /*
        |--------------------------------------------------------------------------
        | Product Search Engine
        |--------------------------------------------------------------------------
        */
        $products = Product::where('status', 1)
            ->where(function ($query) use ($words, $search) {
                // Lowercase comparison using LOWER()
                $searchLower = mb_strtolower($search, 'UTF-8');

                $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"]);

                foreach ($words as $word) {
                    $wordLower = mb_strtolower($word, 'UTF-8');
                    $query->orWhereRaw('LOWER(name) LIKE ?', ["%{$wordLower}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$wordLower}%"]);
                }
            })
            ->with('image')
            ->select('id', 'name', 'slug', 'new_price', 'old_price', 'stock')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Response Handling
        |--------------------------------------------------------------------------
        */
        if ($products->isEmpty()) {
            return response()->json([
                'status'   => true,
                'type'     => 'no_products',
                'message'  => 'দুঃখিত, এই মুহূর্তে এই নামের কোনো পণ্য পাওয়া যায়নি।',
                'products' => [],
            ]);
        }

        $result = $products->map(function ($product) {
            return [
                'id'           => (string) $product->id,
                'name'         => $product->name,
                'price'        => (float) $product->new_price,
                'old_price'    => $product->old_price ? (float) $product->old_price : null,
                'stock'        => (int) $product->stock,
                'availability' => $product->stock > 0 ? 'in stock' : 'out of stock',
                'url'          => url('/product/' . $product->slug),
                'image'        => $product->image ? asset($product->image->image) : null,
            ];
        })->values();

        return response()->json([
            'status'   => true,
            'type'     => 'products',
            'message'  => 'আপনার অনুসন্ধানের ভিত্তিতে প্রাপ্ত পণ্যসমূহ:',
            'search'   => $search,
            'count'    => $result->count(),
            'products' => $result,
        ]);
    }
    public function apiOrderSave(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string',
            'phone'                => 'required|string',
            'address'              => 'required|string',
            'product_id'           => 'required|exists:products,id',
            'quantity'             => 'nullable|integer|min:1',
            'shipping_district_id' => 'nullable|integer',
            'thana_id'             => 'nullable|integer',
        ]);

        // ১. কাস্টমার চেক ও অ্যাকাউন্ট ব্লকিং লজিক
        $customer = Customer::where('phone', $request->phone)->first();
        if ($customer && $customer->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'আপনার অ্যাকাউন্টটি স্থগিত করা হয়েছে। আপনি অর্ডার করতে পারবেন না।'
            ], 403);
        }

        if (!$customer) {
            $customer           = new Customer();
            $customer->name     = $request->name;
            $customer->slug     = Str::slug($request->name);
            $customer->phone    = $request->phone;
            $customer->password = bcrypt(rand(111111, 999999));
            $customer->verify   = 1;
            $customer->status   = 'active';
            $customer->ip_address = $request->ip();
            $customer->save();
        }
        $customer_id = $customer->id;

        // ২. অটোমেটিক জেলা ও থানা এক্সট্র্যাক্ট লজিক (ঠিকানা থেকে ম্যাচ করা)
        $detectedLocation = $this->detectDistrictAndThana($request->address);

        $shipping_district_id = $request->input('shipping_district_id') ?? $detectedLocation['district_id'] ?? NULL;
        $thana_id             = $request->input('thana_id') ?? $detectedLocation['thana_id'] ?? NULL;

        // ৩. প্রোডাক্ট তথ্য ও শিপিং হিসাব
        $product  = Product::find($request->product_id);
        $quantity = $request->input('quantity', 1);
        $subtotal = $product->new_price * $quantity;

        $shippingfee = 120;
        $district    = ShippingDistrict::find($shipping_district_id);
        $thana       = Thana::find($thana_id);

        if ($district && (
            strtolower(trim($district->name_en ?? '')) == 'dhaka' || 
            trim($district->name_bn ?? '') == 'ঢাকা'
        )) {
            $thanaNameEn = strtolower(trim($thana->name_en ?? ''));
            if ($thana && in_array($thanaNameEn, ['savar', 'ashulia', 'dohar', 'hemayetpur', 'keraniganj'])) {
                $shippingfee = 110;
            } else {
                $shippingfee = 80;
            }
        }

        $total_amount = $subtotal + $shippingfee;

        // ৪. ইউনিক ইনভয়েস আইডি জেনারেশন ও অর্ডার ক্রিয়েশন
        do {
            $invoice_id = rand(11111, 99999);
        } while (Order::where('invoice_id', $invoice_id)->exists());

        $order                  = new Order();
        $order->invoice_id      = $invoice_id;
        $order->amount          = $total_amount;
        $order->discount        = 0;
        $order->shipping_charge = $shippingfee;
        $order->customer_id     = $customer_id;
        $order->order_status    = 1;
        $order->order_note      = 'Order via Messenger Chatbot';
        $order->client_ip       = $request->ip();
        $order->user_agent      = 'Messenger AI Agent';
        $order->save();

        // ৫. শিপিং টেবিল সেভ
        $shipping                       = new Shipping();
        $shipping->order_id             = $order->id;
        $shipping->customer_id          = $customer_id;
        $shipping->name                 = $request->name;
        $shipping->phone                = $request->phone;
        $shipping->shipping_district_id = $shipping_district_id;
        $shipping->thana_id             = $thana_id;
        $shipping->address              = $request->address;
        $shipping->save();

        // ৬. পেমেন্ট রেকর্ড
        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = 'cod'; // Cash on Delivery
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        // ৭. অর্ডার ডিটেইলস
        $order_details                = new OrderDetails();
        $order_details->order_id      = $order->id;
        $order_details->product_id    = $product->id;
        $order_details->product_name  = $product->name;
        $order_details->purchase_price = $product->purchase_price ?? 0;
        $order_details->sale_price    = $product->new_price;
        $order_details->qty           = $quantity;
        $order_details->save();

        // ৮. BD COURIER FRAUD CHECK
        try {
            $apiKey = env('BD_COURIER_API_KEY');
            if (!empty($apiKey)) {
                $response = Http::withToken($apiKey)->timeout(10)->post('https://bdcourier.com/api/courier-check', [
                    'phone' => $request->phone
                ]);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if (isset($apiData['courierData']) && !empty($apiData['courierData'])) {
                        $totalParcels = 0;
                        $totalSuccessfulParcels = 0;
                        foreach ($apiData['courierData'] as $courier) {
                            $totalParcels += (int)($courier['total_parcel'] ?? 0);
                            $totalSuccessfulParcels += (int)($courier['success_parcel'] ?? 0);
                        }
                        $successRate = $totalParcels > 0 ? ($totalSuccessfulParcels / $totalParcels) * 100 : 0;

                        if ($totalSuccessfulParcels < 2 || ($totalParcels >= 3 && $successRate < 80)) {
                            return response()->json([
                                'status'         => true,
                                'fraud_detected' => true,
                                'invoice_id'     => $order->invoice_id,
                                'message'        => "আপনার অর্ডারটি প্রসেস করতে ডেলিভারি চার্জ অগ্রিম (১২০ টাকা) দিতে হবে। বিকাশ/নগদে ডেলিভারি চার্জ পাঠালে অর্ডার কনফার্ম হবে।"
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('BD Courier API Error: ' . $e->getMessage());
        }

        // ৮. SMS নোটিফিকেশন পাঠানো
        // $sms_gateway = SmsGateway::where(['status' => 1, 'order' => '1'])->first();
        // if ($sms_gateway) {
        //     $customer_name = Str::limit($request->name, 15, '...');
        //     $url           = "$sms_gateway->url";
        //     $sms_data      = [
        //         "api_key"  => "$sms_gateway->api_key",
        //         "senderid" => "$sms_gateway->serderid",
        //         "number"   => $request->phone,
        //         "message"  => "Dear $customer_name, Thanks for shopping with Smart Bangla Shop! Order ID: #$order->invoice_id, Total Bill: $order->amount Tk."
        //     ];

        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //     curl_exec($ch);
        //     curl_close($ch);
        // }

        return response()->json([
            'status'         => true,
            'fraud_detected' => false,
            'invoice_id'     => $order->invoice_id,
            'total_amount'   => $order->amount,
            'product'        => $product->name,
            'message'        => "অর্ডার সফলভাবে সম্পন্ন হয়েছে! আপনার অর্ডার আইডি: #{$order->invoice_id}।"
        ]);
    }

    /**
     * ঠিকানা থেকে অটোমেটিক ডিস্ট্রিক্ট ও থানা খুঁজে বের করার প্রাইভেট মেথড
     */
    private function detectDistrictAndThana($address)
    {
        $addressLower = mb_strtolower($address, 'UTF-8');

        $detectedDistrictId = null;
        $detectedThanaId    = null;

        // ১. থানা ম্যাচিং (থানার টেবিল চেক)
        $thanas = Thana::all();
        foreach ($thanas as $thana) {
            $nameEn = mb_strtolower($thana->name_en ?? '', 'UTF-8');
            $nameBn = mb_strtolower($thana->name_bn ?? '', 'UTF-8');

            if (($nameEn && str_contains($addressLower, $nameEn)) || ($nameBn && str_contains($addressLower, $nameBn))) {
                $detectedThanaId    = $thana->id;
                $detectedDistrictId = $thana->shipping_district_id ?? $thana->district_id; // আপনার টেবিলের কলাম অনুযায়ী
                break;
            }
        }

        // ২. যদি থানা থেকে ডিস্ট্রিক্ট না পাওয়া যায়, ডিস্ট্রিক্ট টেবিল ম্যাচিং
        if (!$detectedDistrictId) {
            $districts = ShippingDistrict::all();
            foreach ($districts as $district) {
                $distEn = mb_strtolower($district->name_en ?? '', 'UTF-8');
                $distBn = mb_strtolower($district->name_bn ?? '', 'UTF-8');

                if (($distEn && str_contains($addressLower, $distEn)) || ($distBn && str_contains($addressLower, $distBn))) {
                    $detectedDistrictId = $district->id;
                    break;
                }
            }
        }

        return [
            'district_id' => $detectedDistrictId,
            'thana_id'    => $detectedThanaId
        ];
    }
}