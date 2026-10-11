<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAnalytics;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function track(Request $request)
    {
        $exists = ProductAnalytics::where('product_id', $request->product_id)
            ->where('event', $request->event)
            ->where('ip_address', $request->ip())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($exists) {
            return response()->json(['status' => 'duplicate ignored']);
        }

        ProductAnalytics::create([
            'product_id' => $request->product_id,
            'event' => $request->event,
            'value' => $request->value,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
