<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order; 
use App\Models\BusinessSetting; 
use App\Models\OrderStatus;

class FraudCheckController extends Controller
{
    public function config()
    {
        return view('backEnd.admin.fraud.config');
    }

    public function update(Request $request)
    {
        $this->updateSetting('bd_courier_api_token', $request->bd_courier_api_token);
        $this->updateSetting('bd_courier_status', $request->has('bd_courier_status') ? 1 : 0);
        $this->updateSetting('manual_check_link', $request->manual_check_link);
        $this->updateSetting('manual_check_status', $request->has('manual_check_status') ? 1 : 0);

        return back()->with('success', 'Settings updated successfully');
    }

    public function checkCustomer(Request $request)
    {
        $phone = $request->phone;
        
        try {
            // --- পার্ট ১: Shop History ---
            $total = 0; $success = 0; $cancel = 0;
            try {
                 $successIds = OrderStatus::where('name', 'LIKE', '%Deliver%')
                                ->orWhere('name', 'LIKE', '%Complete%')
                                ->orWhere('name', 'LIKE', '%Success%')->pluck('id')->toArray();
                 $cancelIds = OrderStatus::where('name', 'LIKE', '%Cancel%')
                                ->orWhere('name', 'LIKE', '%Return%')
                                ->orWhere('name', 'LIKE', '%Fail%')->pluck('id')->toArray();
                
                 if(empty($successIds)) $successIds = [5]; 
                 if(empty($cancelIds)) $cancelIds = [6, 8]; 

                 $orders = Order::query();
                 $orders->whereHas('shipping', function($q) use ($phone){
                     $q->where('phone', 'LIKE', "%{$phone}%");
                 });

                 $ordersData = $orders->get();
                 $total = $ordersData->count();
                 $success = $ordersData->whereIn('order_status', $successIds)->count();
                 $cancel = $ordersData->whereIn('order_status', $cancelIds)->count();
            } catch (\Exception $ex) { }

            // --- পার্ট ২: BD Courier API Fix (Summary Support) ---
            $apiData = null;
            $apiErrorMsg = "";
            $bdStatus = 0;
            
            if(class_exists('\App\Models\BusinessSetting')){
                 $bdStatus = \App\Models\BusinessSetting::where('type', 'bd_courier_status')->value('value');
            }
            
            if ($bdStatus == 1) {
                $token = \App\Models\BusinessSetting::where('type', 'bd_courier_api_token')->value('value');
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->post('https://api.bdcourier.com/courier-check', [ 
                        'phone' => $phone
                    ]);
                    
                    if ($response->successful()) {
                        $rawData = $response->json(); 
                        
                        // **FIX: ডাটা পার্সিং লজিক**
                        $summary = [];
                        // ১. যদি ডাটা 'data.summary' তে থাকে (আপনার ক্ষেত্রে এটাই হচ্ছে)
                        if (isset($rawData['data']['summary'])) {
                            $summary = $rawData['data']['summary'];
                        } 
                        // ২. যদি ডাটা সরাসরি 'summary' তে থাকে
                        elseif (isset($rawData['summary'])) {
                            $summary = $rawData['summary'];
                        }
                        // ৩. যদি ডাটা সরাসরি 'data' তে থাকে
                        elseif (isset($rawData['data'])) {
                            $summary = $rawData['data'];
                        }

                        $p_total = $summary['total_parcel'] ?? $summary['total'] ?? 0;
                        $p_success = $summary['success_parcel'] ?? $summary['success'] ?? 0;
                        $p_cancel = $summary['cancelled_parcel'] ?? $summary['cancel'] ?? 0;
                        
                        $apiData = [
                            'status' => 'success',
                            'total' => $p_total,
                            'success' => $p_success,
                            'cancel' => $p_cancel,
                            'rate' => ($p_total > 0) ? round(($p_success / $p_total) * 100) : 0
                        ];

                    } else {
                        $apiErrorMsg = "Status: " . $response->status();
                    }
                } catch (\Exception $e) {
                    $apiErrorMsg = "Conn Error";
                }
            } else {
                $apiErrorMsg = "Disabled";
            }
            
            // ম্যানুয়াল লিংক
            $manualLink = '';
            $manualStatus = 0;
            if(class_exists('\App\Models\BusinessSetting')){
                $manualLink = \App\Models\BusinessSetting::where('type', 'manual_check_link')->value('value');
                $manualStatus = \App\Models\BusinessSetting::where('type', 'manual_check_status')->value('value');
            }

            return response()->json([
                'internal' => ['total' => $total, 'success' => $success, 'cancel' => $cancel],
                'external' => $apiData,
                'api_msg' => $apiErrorMsg,
                'manual_link' => $manualLink,
                'manual_status' => $manualStatus,
                'phone' => $phone
            ]);

        } catch (\Exception $e) {
            return response()->json(['internal' => ['total' => 'Error', 'success' => 0, 'cancel' => 0]]);
        }
    }
    
    private function updateSetting($key, $value) {
        $setting = BusinessSetting::where('type', $key)->first();
        if ($setting != null) {
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = new BusinessSetting;
            $setting->type = $key;
            $setting->value = $value;
            $setting->save();
        }
    }
}