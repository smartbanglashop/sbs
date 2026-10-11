<?php

namespace App\Http\Controllers\Frontend;

use shurjopayv2\ShurjopayLaravelPackage8\Http\Controllers\ShurjopayController;
use App\Mail\OrderPlace;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use App\Models\Customer;
use App\Models\District;
use App\Models\Order;
use App\Models\ShippingCharge;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\Review;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\ShippingDistrict;
use App\Models\Thana;
use Session;
use Hash;
use Auth;
use Cart;
use Mail;
use Str;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Services\MetaConversionApi;

class CustomerController extends Controller
{
    function __construct()
    {
        $this->middleware('customer', ['except' => ['register', 'store', 'verify', 'resendotp', 'account_verify', 'login', 'signin', 'logout', 'checkout', 'forgot_password', 'forgot_verify', 'forgot_reset', 'forgot_store', 'forgot_resend', 'order_save', 'order_success', 'order_track', 'order_track_result','saveGuestSession','getThana', 'fraudPaymentConfirm']]);
    }

    public function review(Request $request)
    {
        $this->validate($request, [
            'ratting' => 'required',
            'review' => 'required',
        ]);

        $review              =   new Review();
        $review->name        =   Auth::guard('customer')->user()->name ? Auth::guard('customer')->user()->name : 'N / A';
        $review->email       =   Auth::guard('customer')->user()->email ? Auth::guard('customer')->user()->email : 'N / A';
        $review->product_id  =   $request->product_id;
        $review->review      =   $request->review;
        $review->ratting     =   $request->ratting;
        $review->customer_id =   Auth::guard('customer')->user()->id;
        $review->status      =   'pending';
        $review->save();

        Toastr::success('Thanks, Your review send successfully', 'Success!');
        return redirect()->back();
    }

    public function login()
    {
        return view('frontEnd.layouts.customer.login');
    }

    public function signin(Request $request)
    {
        $auth_check = Customer::where('phone', $request->phone)->first();
        if ($auth_check) {
            if (Auth::guard('customer')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
                Toastr::success('You are login successfully', 'success!');
                if (Cart::instance('shopping')->count() > 0) {
                    return redirect()->route('customer.checkout');
                }
                return redirect()->intended('customer/account');
            }
            Toastr::error('message', 'Opps! your phone or password wrong');
            return redirect()->back();
        } else {
            Toastr::error('message', 'Sorry! You have no account');
            return redirect()->back();
        }
    }

    public function register()
    {
        return view('frontEnd.layouts.customer.register');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required',
            'phone'    => 'required|unique:customers',
            'password' => 'required|min:6'
        ]);

        $last_id = Customer::orderBy('id', 'desc')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;
        $store              = new Customer();
        $store->name        = $request->name;
        $store->slug        = strtolower(Str::slug($request->name . '-' . $last_id));
        $store->phone       = $request->phone;
        $store->email       = $request->email;
        $store->password    = bcrypt($request->password);
        $store->verify      = 1;
        $store->status      = 'active';
        $store->save();

        Toastr::success('Success', 'Account Create Successfully');
        return redirect()->route('customer.login');
    }

    public function verify()
    {
        return view('frontEnd.layouts.customer.verify');
    }

    public function resendotp(Request $request)
    {
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();
        $customer_info->verify = rand(1111, 9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where('status', 1)->first();
        if ($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "contacts" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "msg" => "Dear $customer_info->name!\r\nYour account verify OTP is $customer_info->verify \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        Toastr::success('Success', 'Resend code send successfully');
        return redirect()->back();
    }

    public function account_verify(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required',
        ]);
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();
        if ($customer_info->verify != $request->otp) {
            Toastr::error('Success', 'Your OTP not match');
            return redirect()->back();
        }

        $customer_info->verify = 1;
        $customer_info->status = 'active';
        $customer_info->save();
        Auth::guard('customer')->loginUsingId($customer_info->id);
        return redirect()->route('customer.account');
    }

    public function forgot_password()
    {
        return view('frontEnd.layouts.customer.forgot_password');
    }

    public function forgot_verify(Request $request)
    {
        $customer_info = Customer::where('phone', $request->phone)->first();
        if (!$customer_info) {
            Toastr::error('Your phone number not found');
            return back();
        }
        $customer_info->forgot = rand(1111, 9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status' => 1, 'forget_pass' => 1])->first();
        if ($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "contacts" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "msg" => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        session::put('verify_phone', $request->phone);
        Toastr::success('Your account register successfully');
        return redirect()->route('customer.forgot.reset');
    }

    public function forgot_resend(Request $request)
    {
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();
        $customer_info->forgot = rand(1111, 9999);
        $customer_info->save();
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status' => 1])->first();
        if ($sms_gateway) {
            $url = "$sms_gateway->url";
            $data = [
                "api_key" => "$sms_gateway->api_key",
                "contacts" => $customer_info->phone,
                "type" => 'text',
                "senderid" => "$sms_gateway->serderid",
                "msg" => "Dear $customer_info->name!\r\nYour forgot password verify OTP is $customer_info->forgot \r\nThank you for using $site_setting->name"
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        Toastr::success('Success', 'Resend code send successfully');
        return redirect()->back();
    }

    public function forgot_reset()
    {
        if (!Session::get('verify_phone')) {
            Toastr::error('Something wrong please try again');
            return redirect()->route('customer.forgot.password');
        };
        return view('frontEnd.layouts.customer.forgot_reset');
    }

    public function forgot_store(Request $request)
    {
        $customer_info = Customer::where('phone', session::get('verify_phone'))->first();
        if ($customer_info->forgot != $request->otp) {
            Toastr::error('Success', 'Your OTP not match');
            return redirect()->back();
        }
        $customer_info->forgot = 1;
        $customer_info->password = bcrypt($request->password);
        $customer_info->save();
        if (Auth::guard('customer')->attempt(['phone' => $customer_info->phone, 'password' => $request->password])) {
            Session::forget('verify_phone');
            Toastr::success('You are login successfully', 'success!');
            return redirect()->intended('customer/account');
        }
    }

    public function account()
    {
        return view('frontEnd.layouts.customer.account');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        Toastr::success('You are logout successfully', 'success!');
        return redirect()->route('customer.login');
    }

    public function checkout()
    {
        $checkoutEventId = (string) Str::uuid();

        MetaConversionApi::send(
            'InitiateCheckout',
            $checkoutEventId,
            [
                'currency' => 'BDT',
                'value' => Cart::instance('shopping')->subtotal(),
            ]
        );
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $select_charge = ShippingCharge::where('status', 1)->first();
        $bkash_gateway = PaymentGateway::where(['status' => 1, 'type' => 'bkash'])->first();
        $shurjopay_gateway = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
        Session::put('shipping', $select_charge->amount);
        session([
            'checkout_event_id' => $checkoutEventId,
            'checkout_value' => Cart::instance('shopping')->subtotal(),
        ]);
        $districts=ShippingDistrict::where('status',1)->get();
        return view('frontEnd.layouts.customer.checkout', compact('shippingcharge', 'bkash_gateway', 'shurjopay_gateway','districts'));
    }

    public function order_save(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'shipping_district_id' => 'required',
            'thana_id' => 'required',
        ]);

        // ================= INACTIVE CUSTOMER CHECK =================
        if (Auth::guard('customer')->check()) {
            if (Auth::guard('customer')->user()->status !== 'active') {
                Toastr::error('আপনার অ্যাকাউন্টটি স্থগিত করা হয়েছে। আপনি অর্ডার করতে পারবেন না।', 'দুঃখিত!');
                return redirect()->back()->withInput();
            }
        } else {
            $existingCustomer = Customer::where('phone', $request->phone)->first();
            if ($existingCustomer && $existingCustomer->status !== 'active') {
                Toastr::error('আপনার অ্যাকাউন্টটি স্থগিত করা হয়েছে। আপনি অর্ডার করতে পারবেন না।', 'দুঃখিত!');
                return redirect()->back()->withInput();
            }
        }

        if (Cart::instance('shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('discount');

        $district = ShippingDistrict::find($request->shipping_district_id);
        $thana = Thana::find($request->thana_id);
        $shippingfee = 120;

        if (strtolower(trim($district->name_en)) == 'dhaka') {
            if (in_array(strtolower(trim($thana->name_en)), [
                'savar', 'ashulia', 'dohar', 'hemayetpur', 'keraniganj'
            ])) {
                $shippingfee = 110;
            } else {
                $shippingfee = 80;
            }
        }

        // ================= CUSTOMER তৈরি/আপডেট =================
        if (Auth::guard('customer')->user()) {
            $customer = Auth::guard('customer')->user();
            $customer->update(['ip_address' => $request->ip()]);
            $customer_id = $customer->id;
        } else {
            $customer = Customer::where('phone', $request->phone)->first();
            if ($customer) {
                $customer->update(['ip_address' => $request->ip()]);
                $customer_id = $customer->id;
            } else {
                $password = rand(111111, 999999);
                $store              = new Customer();
                $store->name        = $request->name;
                $store->slug        = $request->name;
                $store->phone       = $request->phone;
                $store->password    = bcrypt($password);
                $store->verify      = 1;
                $store->status      = 'active';
                $store->ip_address  = $request->ip();
                $store->save();
                $customer_id = $store->id;
            }
        }

        // ================= ORDER + SHIPPING + PAYMENT + DETAILS তৈরি (ফ্রড চেকের আগেই) =================
        $order = new Order();
        do {
            $invoice_id = rand(11111, 99999);
        } while (Order::where('invoice_id', $invoice_id)->exists());
        $order->invoice_id      = $invoice_id;
        $order->amount          = ($subtotal + $shippingfee) - $discount;
        $order->discount        = $discount ? $discount : 0;
        $order->shipping_charge = $shippingfee;
        $order->customer_id     = $customer_id;
        $order->order_status    = 1;
        $order->order_note      = $request->order_note;
        $order->fbp             = $_COOKIE['_fbp'] ?? null;

        if (isset($_COOKIE['_fbc'])) {
            $order->fbc = $_COOKIE['_fbc'];
        } elseif (request()->has('fbclid')) {
            $order->fbc = 'fb.1.' . time() . '.' . request()->fbclid;
        }

        $order->client_ip  = request()->ip();
        $order->user_agent = request()->userAgent();
        $order->save();

        $shipping                       = new Shipping();
        $shipping->order_id             = $order->id;
        $shipping->customer_id          = $customer_id;
        $shipping->name                 = $request->name;
        $shipping->phone                = $request->phone;
        $shipping->shipping_district_id = $request->shipping_district_id;
        $shipping->thana_id             = $request->thana_id;
        $shipping->address              = $request->address;
        $shipping->save();

        $payment                 = new Payment();
        $payment->order_id       = $order->id;
        $payment->customer_id    = $customer_id;
        $payment->payment_method = $request->payment_method;
        $payment->amount         = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        foreach (Cart::instance('shopping')->content() as $cart) {
            $order_details                 = new OrderDetails();
            $order_details->order_id       = $order->id;
            $order_details->product_id     = $cart->id;
            $order_details->product_name   = $cart->name;
            $order_details->purchase_price = $cart->options->purchase_price;
            $order_details->product_color  = $cart->options->product_color;
            $order_details->product_size   = $cart->options->product_size;
            $order_details->sale_price     = $cart->price;
            $order_details->qty            = $cart->qty;
            $order_details->save();
        }

        // ================= START: BD COURIER FRAUD CHECK =================
        try {
            $phoneNumber = $request->phone;
            $apiKey = env('BD_COURIER_API_KEY');

            if (!empty($apiKey)) {
                $response = Http::withToken($apiKey)->timeout(10)->post('https://bdcourier.com/api/courier-check', [
                    'phone' => $phoneNumber
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

                        $isFraud = false;
                        if ($totalSuccessfulParcels < 2) {
                            $isFraud = true;
                        }
                        if ($totalParcels >= 3 && $successRate < 80) {
                            $isFraud = true;
                        }

                        if ($isFraud) {
                            $msg = "অপ্রয়োজনীয় ফেইক অর্ডার এড়াতে এবং নিশ্চিত ক্রেতাদের দ্রুত সেবা দিতে আমাদের প্রতিটি অর্ডারের ক্ষেত্রে শুধুমাত্র ডেলিভারি চার্জ অগ্রিম প্রযোজ্য। বাকি টাকা পণ্য হাতে পেয়ে পরিশোধ করা যাবে। নিচে দেওয়া নাম্বারে ডেলিভারি চার্জ ১২০৳ সেন্ড মানি করে ট্রানজেকশন আইডি (TrxID) দিয়ে আপনার অর্ডারটি কনফার্ম করুন।";

                            return redirect()->back()->with([
                                'fraud_detected'   => $msg,
                                'pending_order_id' => $order->id,
                            ])->withInput();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('BD Courier API Error: ' . $e->getMessage());
        }
        // ================= END: BD COURIER FRAUD CHECK =================

        // ================= ফ্রড না থাকলে স্বাভাবিক ফ্লো =================
        $productIds = OrderDetails::where('order_id', $order->id)
            ->pluck('product_id')
            ->map(fn ($id) => (string) $id)
            ->toArray();
        $leadEventId = (string) Str::uuid();

        MetaConversionApi::send(
            'Lead',
            $leadEventId,
            [
                'currency' => 'BDT',
                'value' => $order->amount,
                'content_ids' => $productIds,
                'content_type' => 'product',
            ],
            $order->id
        );

        session([
            'lead_event_id' => $leadEventId,
            'lead_content_ids' => $productIds,
            'lead_value' => $order->amount,
        ]);

        Cart::instance('shopping')->destroy();
        session()->forget(['guest_name', 'guest_phone', 'guest_address']);
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        $site_setting = GeneralSetting::where('status', 1)->first();
        $sms_gateway = SmsGateway::where(['status' => 1, 'order' => '1'])->first();
        $contact = Contact::where('status', 1)->first();
        if ($contact->email) {
            try {
                Mail::to($contact->email)->send(new OrderPlace($order));
            } catch (Exception $e) {
                Log::error('Email sending failed: ' . $e->getMessage());
            }
        }

        if ($sms_gateway) {
            $customer_name = \Illuminate\Support\Str::limit($request->name, 15, '...');

            $url = "$sms_gateway->url";
            $sms_data = [
                "api_key"  => "$sms_gateway->api_key",
                "senderid" => "$sms_gateway->serderid",
                "number"   => $request->phone,
                "message"  => "Dear $customer_name, Thanks for shopping with Smart Bangla Shop! Order ID: #$order->invoice_id, Total Bill: $order->amount Tk. Tracking: https://smartbanglashop.com/customer/order-track"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        }

        if ($request->payment_method == 'bkash') {
            return redirect('/bkash/checkout-url/create?order_id=' . $order->id);
        } elseif ($request->payment_method == 'shurjopay') {
            $info = array(
                'currency' => "BDT",
                'amount' => $order->amount,
                'order_id' => uniqid(),
                'discsount_amount' => 0,
                'disc_percent' => 0,
                'client_ip' => $request->ip(),
                'customer_name' =>  $request->name,
                'customer_phone' => $request->phone,
                'email' => "customer@gmail.com",
                'customer_address' => $request->address,
                'customer_city' => $request->area,
                'customer_state' => $request->area,
                'customer_postcode' => "1212",
                'customer_country' => "BD",
                'value1' => $order->id
            );
            $shurjopay_service = new ShurjopayController();
            return $shurjopay_service->checkout($info);
        } else {
            return redirect('customer/order-success/' . $order->id);
        }
    }

    public function orders()
    {
        $orders = Order::where('customer_id', Auth::guard('customer')->user()->id)->with('status')->latest()->get();
        return view('frontEnd.layouts.customer.orders', compact('orders'));
    }

    public function order_success($id)
    {
        $order = Order::where('id', $id)->firstOrFail();
        return view('frontEnd.layouts.customer.order_success', compact('order'));
    }

    public function invoice(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'customer_id' => Auth::guard('customer')->user()->id])->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('frontEnd.layouts.customer.invoice', compact('order'));
    }

    public function order_note(Request $request)
    {
        $order = Order::where(['id' => $request->id, 'customer_id' => Auth::guard('customer')->user()->id])->firstOrFail();
        return view('frontEnd.layouts.customer.order_note', compact('order'));
    }

    public function profile_edit(Request $request)
    {
        $profile_edit = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();
        $districts = District::distinct()->select('district')->get();
        $areas = District::where(['district' => $profile_edit->district])->select('area_name', 'id')->get();
        return view('frontEnd.layouts.customer.profile_edit', compact('profile_edit', 'districts', 'areas'));
    }

    public function profile_update(Request $request)
    {
        $update_data = Customer::where(['id' => Auth::guard('customer')->user()->id])->firstOrFail();
        $image = $request->file('image');
        if ($image) {
            $name =  time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(Str::slug($name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $img->resize(120, 120);
            $img->save($imageUrl);
        } else {
            $imageUrl = $update_data->image;
        }

        $update_data->name        =   $request->name;
        $update_data->phone       =   $request->phone;
        $update_data->email       =   $request->email;
        $update_data->address     =   $request->address;
        $update_data->district    =   $request->district;
        $update_data->area        =   $request->area;
        $update_data->image       =   $imageUrl;
        $update_data->save();

        Toastr::success('Your profile update successfully', 'Success!');
        return redirect()->route('customer.account');
    }

    public function order_track()
    {
        return view('frontEnd.layouts.customer.order_track');
    }

    public function order_track_result(Request $request)
    {
        $phone = $request->phone;
        $invoice_id = $request->invoice_id;

        $query = DB::table('orders')
            ->join('shippings', 'orders.id', '=', 'shippings.order_id')
            ->select('orders.*', 'shippings.phone');

        if (!empty($phone)) {
            $query->where('shippings.phone', $phone);
        }

        if (!empty($invoice_id)) {
            $query->where('orders.invoice_id', $invoice_id);
        }

        $order = $query->first();

        if (!$order) {
            Toastr::error('Order not found!');
            return back();
        }

        return view('frontEnd.layouts.customer.tracking_result', compact('order'));
    }

    public function change_pass()
    {
        return view('frontEnd.layouts.customer.change_password');
    }

    public function password_update(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required_with:new_password|same:new_password|'
        ]);

        $customer = Customer::find(Auth::guard('customer')->user()->id);
        if (Hash::check($request->old_password, $customer->password)) {
            $customer->fill(['password' => Hash::make($request->new_password)])->save();
            Toastr::success('Success', 'Password changed successfully!');
            return redirect()->route('customer.account');
        } else {
            Toastr::error('Failed', 'Old password not match!');
            return redirect()->back();
        }
    }

    public function saveGuestSession(Request $request)
    {
        if ($request->has('guest_name')) {
            session(['guest_name' => $request->guest_name]);
        }
        if ($request->has('guest_phone')) {
            session(['guest_phone' => $request->guest_phone]);
        }
        if ($request->has('guest_address')) {
            session(['guest_address' => $request->guest_address]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Guest session updated successfully'
        ]);
    }

    public function getThana($district_id)
    {
        $thanas = Thana::where('shipping_district_id', $district_id)
                    ->where('status', 1)
                    ->orderBy('name_bn','ASC')
                    ->get();

        $output = '<option value="">থানা নির্বাচন করুন</option>';

        foreach ($thanas as $thana) {
            $output .= '<option value="'.$thana->id.'"
            data-name="'.strtolower($thana->name_en).'">'
            .$thana->name_en.' - '.$thana->name_bn.
'</option>';
        }

        return response($output);
    }

    // ================= Fraud Payment Confirmation =================
    public function fraudPaymentConfirm(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'transaction_id' => 'required|string|max:50',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $cleanTrxId = strtoupper(trim($request->transaction_id));

            // Order note এ Transaction ID যোগ করা
            $order->update([
                'order_note' => trim($order->order_note . "\nBkash TrxID: " . $cleanTrxId),
            ]);

            // Payment status update করা
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'payment_status' => 'completed',
                    'trx_id'         => $cleanTrxId,
                ]);
            }

            // কার্ট খালি করা
            Cart::instance('shopping')->destroy();
            session()->forget(['guest_name', 'guest_phone', 'guest_address']);

            // ================= 🆕 ইমেইল পাঠানো =================
            $contact = Contact::where('status', 1)->first();
            if ($contact && $contact->email) {
                try {
                    Mail::to($contact->email)->send(new OrderPlace($order));
                } catch (Exception $e) {
                    Log::error('Fraud Order Email sending failed: ' . $e->getMessage());
                }
            }
            // ====================================================

            // SMS পাঠানো
            $site_setting = GeneralSetting::where('status', 1)->first();
            $sms_gateway = SmsGateway::where(['status' => 1, 'order' => '1'])->first();

            if ($sms_gateway) {
                $customer_name = \Illuminate\Support\Str::limit($order->shipping->name ?? 'Customer', 15, '...');

                $url = "$sms_gateway->url";
                $sms_data = [
                    "api_key"  => "$sms_gateway->api_key",
                    "senderid" => "$sms_gateway->serderid",
                    "number"   => $order->shipping->phone ?? '',
                    "message" => "Dear " . substr($customer_name, 0, 15) . ", Thanks for shopping with Smart Bangla Shop! Order ID: #$order->invoice_id, Total Bill: $order->amount Tk. Tracking: https://smartbanglashop.com/customer/order-track",
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $sms_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);
                curl_close($ch);
            }

            Toastr::success('ধন্যবাদ! আপনার অর্ডারটি সফলভাবে নিশ্চিত হয়েছে।', 'Success!');
            return redirect()->route('customer.order_success', $order->id);

        } catch (\Exception $e) {
            Log::error('Fraud Payment Confirm Error: ' . $e->getMessage());
            Toastr::error('অর্ডার সাবমিট করতে সমস্যা হয়েছে। আবার চেষ্টা করুন।', 'Failed!');
            return redirect()->back();
        }
    }
    // ================= শেষ =================
}