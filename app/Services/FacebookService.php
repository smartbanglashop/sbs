<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;

// class FacebookService
// {
//     public function verifyPixel($pixelId, $accessToken)
//     {
//         $url = "https://graph.facebook.com/v23.0/{$pixelId}";

//         return Http::get($url, [
//             'access_token' => $accessToken
//         ]);
//     }
//     public function debugToken($accessToken)
//     {
//         $url = "https://graph.facebook.com/debug_token";

//         $response = Http::get($url, [
//             'input_token' => $accessToken,
//             'access_token' => $accessToken,
//         ]);

//         return $response;
//     }
// }


// newly added 


namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacebookService
{
    /**
     * Debug / Validate Access Token
     */
    public function debugToken($accessToken)
    {
        return Http::get("https://graph.facebook.com/debug_token", [
            'input_token' => $accessToken,
            'access_token' => $accessToken,
        ]);
    }

    /**
     * Send Test Event to Pixel (REAL verification method)
     */
  public function sendTestEvent($pixelId, $accessToken, $testEventCode = null)
{
    $payload = [
        'data' => [
            [
                'event_name' => 'PageView',
                'event_time' => time(),
                'action_source' => 'website',

                // 🔥 IMPORTANT (Meta now expects this)
                'event_source_url' => url('/'),

                // 🔥 Minimal user_data (fixes Invalid parameter issue)
                'user_data' => [
                    'client_ip_address' => request()->ip(),
                    'client_user_agent' => request()->userAgent(),
                ],
            ]
        ],
        'access_token' => $accessToken,
    ];

    if ($testEventCode) {
        $payload['test_event_code'] = $testEventCode;
    }

    return Http::post(
        "https://graph.facebook.com/v23.0/{$pixelId}/events",
        $payload
    );
}
}
