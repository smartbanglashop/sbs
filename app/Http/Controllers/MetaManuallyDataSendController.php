<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MetaConversionApi;
use Illuminate\Http\Request;

class MetaManuallyDataSendController extends Controller
{


    public function metaUnsent()
    {
        $show_data = Order::where('meta_purchase_sent', 0)
    ->where('order_status', '!=', 8)
    ->where('order_status', '!=', 1)
    ->with(['shipping', 'status'])
    ->latest()
    ->take(30)
    ->get();

        return view(
            'backEnd.order.unsend_data_index',
            compact('show_data')
        );
    }

    public function sendMetaPurchase($id)
    {
        $order = Order::find($id);

        if (!$order) {

            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if ((int) $order->meta_purchase_sent === 1) {

            return response()->json([
                'status' => false,
                'message' => 'This order has already been sent to Meta.',
            ]);
        }

        $customData = [
            'value' => (float) $order->amount,
            'currency' => 'BDT',
        ];

        $result = MetaConversionApi::send(
            'Purchase',
            'purchase_' . $order->id,
            $customData,
            $order->id
        );

        if ($result['status']) {

            $order->update([
                'meta_purchase_sent' => 1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Purchase data sent to Meta successfully.',
                'order_id' => $order->id,
                'response' => $result['response'] ?? null,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Meta API request failed.',
            'order_id' => $order->id,
            'response' => $result['response'] ?? null,
        ], 422);
    }




    public function sendAllMetaPurchase(Request $request)
    {
        $orderIds = $request->input('order_ids', []);

        if (!is_array($orderIds) || empty($orderIds)) {

            return response()->json([
                'status' => false,
                'message' => 'Please select at least one order.',
            ], 422);
        }

        $orders = Order::whereIn('id', $orderIds)
            ->where('meta_purchase_sent', 0)
            ->get();


        if ($orders->isEmpty()) {

            return response()->json([
                'status' => false,
                'message' => 'No unsent selected orders found.',
            ], 422);
        }


        $success = 0;
        $failed = 0;

        $failedOrders = [];

        foreach ($orders as $order) {

            try {

                $customData = [
                    'value' => (float) $order->amount,
                    'currency' => 'BDT',
                ];


                $result = MetaConversionApi::send(
                    'Purchase',
                    'purchase_' . $order->id,
                    $customData,
                    $order->id
                );

                if ($result['status']) {

                    $order->update([
                        'meta_purchase_sent' => 1,
                    ]);

                    $success++;
                } else {

                    $failed++;

                    $failedOrders[] = [

                        'order_id' => $order->id,

                        'invoice_id' => $order->invoice_id,

                        'message' =>
                        $result['response']['error']['message']
                            ?? 'Meta API request failed.',

                    ];
                }
            } catch (\Throwable $e) {

                $failed++;

                $failedOrders[] = [

                    'order_id' => $order->id,

                    'invoice_id' => $order->invoice_id,

                    'message' => $e->getMessage(),

                ];
            }
        }


        return response()->json([

            'status' => true,

            'message' =>
            "Meta send complete. Success: {$success}, Failed: {$failed}.",

            'success_count' => $success,

            'failed_count' => $failed,

            'failed_orders' => $failedOrders,

        ]);
    }
}
