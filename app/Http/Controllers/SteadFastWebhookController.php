<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Services\MetaConversionApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Str;

class SteadFastWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify Bearer token
        $token = $request->header('Authorization');

        if ($token !== 'Bearer '.config('steadfast.webhook_bearer_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();

        // Validate required fields
        $required = ['consignment_id', 'invoice', 'status', 'cod_amount', 'updated_at'];
        $missing = array_diff($required, array_keys($payload));

        if (! empty($missing)) {
            return response()->json([
                'error' => 'Missing fields: '.implode(', ', $missing),
            ], 400);
        }

        $this->processStatusUpdate($payload);

        Log::info('SteadFast webhook received', $payload);

        return response()->json(['status' => 'success'], 200);
    }

     private function processStatusUpdate(array $payload): void
     {
         $invoice_id = $payload['invoice'] ?? null;
         $status = $payload['status'] ?? null;
         $tracking_message = $payload['tracking_message'] ?? null;

         if (! $invoice_id) {
             Log::error('SteadFast invoice missing', $payload);

             return;
         }
         $order = Order::where('invoice_id', $invoice_id)->first();

         if (! $order) {
             return;
         }
         $order->update([
             'steadfast_status' => $status,
             'note' => $tracking_message,
         ]);

         if ($status && strtolower($status) == 'delivered' && ! $order->meta_purchase_sent) {
             $purchaseEventId = (string) Str::uuid();
             $productIds = OrderDetails::where('order_id', $order->id)
                 ->pluck('product_id')
                 ->map(fn ($id) => (string) $id)
                 ->toArray();

             $response = MetaConversionApi::send(
                 'Purchase',
                 $purchaseEventId,
                 [
                     'currency' => 'BDT',
                     'value' => $order->amount,
                     'content_ids' => $productIds,
                     'content_type' => 'product',
                 ],
                 $order->id
             );
             if ($response['status']) {
                 $order->update([
                     'meta_purchase_sent' => 1,
                     'order_status' => 6,
                 ]);
             } else {
                 Log::error('Meta Purchase Failed', [
                     'order_id' => $order->id,
                     'response' => $response,
                 ]);
             }
         }
     }
}
