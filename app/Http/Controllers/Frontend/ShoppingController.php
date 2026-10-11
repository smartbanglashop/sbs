<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productprice;
use App\Models\Product;
use Toastr;
use Cart;
use DB;
use App\Services\MetaConversionApi;
use Illuminate\Support\Str;

class ShoppingController extends Controller
{

    public function addTocartGet($id,Request $request){
        $qty=1;
        $productInfo = DB::table('products')->where('id',$id)->first();
        $productImage = DB::table('productimages')->where('product_id',$id)->first();
        $cartinfo=Cart::instance('shopping')->add(['id'=>$productInfo->id,'name'=>$productInfo->name,'qty'=>$qty,'price'=>$productInfo->new_price,
            'options' => [
                'image'=>$productImage->image,
                'old_price'=>$productInfo->old_price,
                'slug' => $productInfo->slug,
                'purchase_price' => $productInfo->purchase_price,
            ]]);
            
        $addToCartEventId = (string) Str::uuid();

       MetaConversionApi::send(
    'AddToCart',
    $addToCartEventId,
    [
        'currency'      => 'BDT',
        'value'         => $productInfo->new_price,
        'content_ids'   => [(string) $productInfo->id],
        'content_type'  => 'product',
        'content_name'  => $productInfo->name,
    ]
);

        // return redirect()->back();
        return response()->json($cartinfo);
    } 

          public function cart_store(Request $request)
    {
        $product = Product::where(['id' => $request->id])->first();
        $addToCartEventId = (string) Str::uuid();

        MetaConversionApi::send(
            'AddToCart',
            $addToCartEventId,
            [
                'currency'      => 'BDT',
                'value'         => $product->new_price,
                'content_ids'   => [(string) $product->id],
                'content_type'  => 'product',
                'content_name'  => $product->name,
            ]
        );

        Cart::instance('shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $request->qty,
            'price' => $product->new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $product->new_price,
                'purchase_price' => $product->purchase_price,
                'product_size' => $request->product_size,
                'product_color' => $request->product_color,
                'pro_unit' => $request->pro_unit,
            ],
        ]);
        session([
            'add_to_cart_event_id' => $addToCartEventId,
            'event_value'          => $product->new_price,
            'content_ids'          => [(string) $product->id],
            'content_name'         => $product->name,
        ]);
        Toastr::success('Product successfully add to cart', 'Success!');
        return redirect()->route('customer.checkout');
    }

    public function cart_update(Request $request)
    {
        // ফিক্স: checkout.blade.php থেকে rowId নামে আইডি আসে, আর কখনো
        // কখনো 'id' নামেও আসতে পারে। দুটোই সাপোর্ট করা হলো।
        $rowId = $request->id ?: $request->rowId;

        // Fetch the current cart item using the row ID
        $cartItem = Cart::instance('shopping')->get($rowId);

        if ($cartItem) {
            // ফিক্স: checkout.blade.php-এর +/− বাটন 'action' প্যারামিটার
            // (increment/decrement) পাঠায়, যা আগে এই মেথড হ্যান্ডেল করত না।
            // এখন action থাকলে qty বাড়ানো/কমানো হবে (ন্যূনতম ১), আর
            // action না থাকলে আগের মতো সাইজ/কালার আপডেট হবে।
            if ($request->action) {
                $qty = $cartItem->qty;

                if ($request->action == 'increment') {
                    $qty = $qty + 1;
                } elseif ($request->action == 'decrement') {
                    $qty = $qty > 1 ? $qty - 1 : 1;
                }

                Cart::instance('shopping')->update($rowId, $qty);
            } else {
                // Update the options for the cart item
                Cart::instance('shopping')->update($rowId, [
                    'options' => [
                        'product_size' => $request->product_size ?: $cartItem->options->product_size, // Use new size or keep existing
                        'product_color' => $request->product_color ?: $cartItem->options->product_color, // Use new color or keep existing
                        'slug' => $cartItem->options->slug, // Keep existing slug
                        'image' => $cartItem->options->image, // Keep existing image
                        'old_price' => $cartItem->options->old_price, // Keep existing old price
                        'purchase_price' => $cartItem->options->purchase_price, // Keep existing purchase price
                        'pro_unit' => $cartItem->options->pro_unit, // Keep existing pro unit
                    ],
                ]);
            }
        }
    
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_remove(Request $request)
    {
        // ফিক্স: 'id' না থাকলে 'rowId' থেকেও নেওয়া হচ্ছে, যাতে
        // checkout.blade.php-এর JS (যেটা rowId নামে পাঠায়) কাজ করে।
        // rowId খালি থাকলে Cart::update(NULL, 0) কল হয়ে ক্র্যাশ করত,
        // তাই null-check বসানো হলো।
        $rowId = $request->id ?: $request->rowId;

        if ($rowId) {
            Cart::instance('shopping')->update($rowId, 0);
        }

        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_increment(Request $request)
    {
        $rowId = $request->id ?: $request->rowId;
        $item = Cart::instance('shopping')->get($rowId);
        if ($item) {
            $qty = $item->qty + 1;
            Cart::instance('shopping')->update($rowId, $qty);
        }
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_decrement(Request $request)
    {
        $rowId = $request->id ?: $request->rowId;
        $item = Cart::instance('shopping')->get($rowId);
        if ($item) {
            $qty = $item->qty > 1 ? $item->qty - 1 : 1;
            Cart::instance('shopping')->update($rowId, $qty);
        }
        $data = Cart::instance('shopping')->content();
        return view('frontEnd.layouts.ajax.cart', compact('data'));
    }

    public function cart_count(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.cart_count', compact('data'));
    }
    public function mobilecart_qty(Request $request)
    {
        $data = Cart::instance('shopping')->count();
        return view('frontEnd.layouts.ajax.mobilecart_qty', compact('data'));
    }
    public function changeProduct(Request $request)
    {
         
        
        // Get the selected product
        $productId = $request->input('id');
        $product = Product::find($productId); // Fetch the product by ID
        
       

        if ($product) {
            // Clear existing items in the cart if necessary
            Cart::instance('shopping')->destroy(); // Or adjust this logic as needed
            
            // Add the selected product to the cart
            Cart::instance('shopping')->add([
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 1, // Adjust quantity as needed
                'price' => $product->new_price,
                'options' => [
                    'slug' => $product->slug,
                    'image' => $product->image->image,
                    'old_price' => $product->old_price,
                    'purchase_price' => $product->purchase_price,
                ],
            ]);
            $data = Cart::instance('shopping')->content();
            return view('frontEnd.layouts.ajax.cart', compact('data'));
           
        }

        return response()->json(['success' => false, 'message' => 'Product not found.']);
    }

}