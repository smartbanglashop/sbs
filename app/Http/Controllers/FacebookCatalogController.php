<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FacebookCatalogController extends Controller
{
    public function feed()
    {
        $products = Product::with(['image', 'category'])
            ->where('status', true)
            ->get();

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=facebook-catalog.csv",
        ];

        $callback = function () use ($products) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'id',
                'title',
                'description',
                'availability',
                'condition',
                'price',
                'link',
                'image_link',
                'brand',
                'google_product_category'
            ]);

            foreach ($products as $product) {

                $image = $product->image
                    ? asset($product->image->image)
                    : '';

                fputcsv($file, [

                  (string)  $product->id,

                    $product->name,

                    strip_tags($product->description),

                    $product->stock > 0 ? 'in stock' : 'out of stock',

                    'new',

                    number_format($product->new_price, 2, '.', '') . ' BDT',

                    route('product', $product->slug),

                    $image,

                    'Smart Bangla',

                    optional($product->category)->name

                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
