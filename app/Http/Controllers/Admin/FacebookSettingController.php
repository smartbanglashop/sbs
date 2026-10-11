<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookSetting;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Toastr;

class FacebookSettingController extends Controller
{
    public function index()
    {
        $setting = FacebookSetting::first();

        return view('backEnd.facebook.setting', compact('setting'));
    }

    public function save(Request $request)
    {
        FacebookSetting::updateOrCreate(

            ['id' => 1],

            [

                'pixel_id' => $request->pixel_id,

                'access_token' => $request->access_token,

                'test_event_code' => $request->test_event_code,

                'browser_tracking' => $request->browser_tracking ?? 0,

                'server_tracking' => $request->server_tracking ?? 0,

                'page_view' => $request->page_view ?? 0,

                'view_content' => $request->view_content ?? 0,

                'add_to_cart' => $request->add_to_cart ?? 0,

                'initiate_checkout' => $request->initiate_checkout ?? 0,

                'purchase' => $request->purchase ?? 0,

                'status' => 1,

            ]

        );

        Toastr::success('Success', 'Data insert successfully');

        return back()->with('success', 'Facebook Setting Saved Successfully');
    }

    // public function verify(FacebookService $facebook)
    // {
    //     $setting = FacebookSetting::first();

    //     if (! $setting) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Facebook configuration not found.',
    //         ]);
    //     }

    //     $debug = $facebook->debugToken($setting->access_token);
    //     $pixel = $facebook->verifyPixel(
    //         $setting->pixel_id,
    //         $setting->access_token
    //     );

    //     if (! ($debug->successful() && $debug->json('data.is_valid'))) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid Access Token',
    //             'debug' => $debug->json(),
    //         ]);
    //     }

    //     if (! $pixel->successful()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Pixel verification failed',
    //             'error' => $pixel->json(),
    //             'debug_token' => $debug->json(),
    //         ]);
    //     }

    //     $setting->update([
    //         'is_connected' => 1,
    //         'last_response' => $pixel->body(),
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Meta Connected Successfully',
    //         'pixel' => $pixel->json(),
    //     ]);
    // }
    
       // newly added

    public function verify(FacebookService $facebook)
    {
        $setting = FacebookSetting::first();

        if (!$setting || !$setting->pixel_id || !$setting->access_token) {
            return response()->json([
                'status' => false,
                'message' => 'Pixel ID or Access Token missing',
            ]);
        }

        // 1. Validate Token
        $debug = $facebook->debugToken($setting->access_token);

        if (!($debug->successful() && $debug->json('data.is_valid'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Access Token',
                'debug' => $debug->json(),
            ]);
        }

        // 2. Send Test Event (REAL verification method)
        $response = $facebook->sendTestEvent(
            $setting->pixel_id,
            $setting->access_token,
            $setting->test_event_code
        );

        if (!$response->successful()) {
            return response()->json([
                'status' => false,
                'message' => 'Pixel connection failed',
                'error' => $response->json(),
                'debug' => $debug->json(),
            ]);
        }

        $setting->update([
            'is_connected' => 1,
            'last_response' => $response->body(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Meta Pixel Connected Successfully',
            'data' => $response->json(),
        ]);
    }
}
