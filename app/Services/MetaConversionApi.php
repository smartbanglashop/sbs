<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\FacebookSetting;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Support\Facades\Http;

class MetaConversionApi
{
    public static function send($eventName, $eventId = null, $customData = [], $orderId = null)
    {
        $setting = FacebookSetting::first();

        if (! $setting) {
            return [
                'status' => false,
                'message' => 'Facebook settings not found.',
            ];
        }

        // ৩. ইউজার কাস্টমার ডাটা সোর্স ইঞ্জিন
        $shippingName = null;
        $shippingAddress = null;
        $userData = [];

        if ($orderId) {
            $order = Order::find($orderId);
            $userData['country'] = hash('sha256', 'bd');

            if ($order) {
                if (! empty($order->client_ip)) {
                    $userData['client_ip_address'] = $order->client_ip;
                }

                if (! empty($order->user_agent)) {
                    $userData['client_user_agent'] = $order->user_agent;
                }

                if (! empty($order->fbp)) {
                    $userData['fbp'] = $order->fbp;
                }

                if (! empty($order->fbc)) {
                    $userData['fbc'] = $order->fbc;
                }
            }

            $shipping = Shipping::with(['district', 'thana'])->where('order_id', $orderId)->first();
            if ($shipping) {
                $shippingName = $shipping->name;
                $shippingAddress = $shipping->address;
                if ($shipping->district) {
                    $userData['ct'] = hash(
                        'sha256',
                        strtolower(trim($shipping->district->name_en))
                    );
                }

                if ($shipping->thana) {
                    $userData['st'] = hash(
                        'sha256',
                        strtolower(trim($shipping->thana->name_en))
                    );
                }

                $customer = Customer::find($shipping->customer_id);
                if ($customer) {
                    if (! empty($customer->id)) {
                        $userData['external_id'] = hash('sha256', (string) $customer->id);
                    }

                    if (! empty($customer->phone)) {
                        $phone = preg_replace('/\D/', '', $customer->phone);
                        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
                            $phone = '88' . $phone;
                        }
                        $userData['ph'] = hash('sha256', $phone);
                    }

                    if (! empty($customer->email)) {
                        $userData['em'] = hash('sha256', strtolower(trim($customer->email)));
                    }
                }
            }
        } else {
            // গেস্ট ইউজারদের বেসিক ব্রাউজার ডাটা আগে ডিক্লেয়ার করা হলো
            $userData = [
                'client_ip_address' => request()->ip(),
                'client_user_agent' => request()->userAgent(),
                'country' => hash('sha256', 'bd'),
                'external_id' => hash('sha256', session()->getId()), // ১০/১০ ইভেন্ট আইডেন্টিফায়ার
            ];

            if (isset($_COOKIE['_fbp'])) {
                $userData['fbp'] = $_COOKIE['_fbp'];
            }

            if (isset($_COOKIE['_fbc'])) {
                $userData['fbc'] = $_COOKIE['_fbc'];
            } elseif (request()->has('fbclid')) {
                $userData['fbc'] = 'fb.1.' . time() . '.' . request()->get('fbclid');
            }

            // এখন সেশন থেকে আসা গেস্ট ডাটাগুলো $userData অ্যারেতে পুশ করা হচ্ছে
            if (session()->has('guest_phone')) {
                $phone = preg_replace('/\D/', '', session('guest_phone'));
                if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
                    $phone = '88' . $phone;
                }
                $userData['ph'] = hash('sha256', $phone);
            }

            if (session()->has('guest_email')) {
                $userData['em'] = hash('sha256', strtolower(trim(session('guest_email'))));
            }

            if (session()->has('guest_name')) {
                $shippingName = session('guest_name');
            }

            if (session()->has('guest_address')) {
                $shippingAddress = session('guest_address');
            }
        }

        // ৪. নাম ফার্স্ট নেম ও লাস্ট নেম হ্যাশ ইঞ্জিন
        if (! empty($shippingName)) {
            $name = explode(' ', trim($shippingName));

            if (isset($name[0])) {
                $userData['fn'] = hash('sha256', strtolower($name[0]));
            }

            if (count($name) > 1) {
                $userData['ln'] = hash(
                    'sha256',
                    strtolower(implode(' ', array_slice($name, 1)))
                );
            }
        }

        // ৫. স্টেট/ঠিকানা হ্যাশ ইঞ্জিন
        if (! empty($shippingAddress)) {
            $userData['st'] = hash('sha256', strtolower($shippingAddress));
        }

        // পেলোড রেডি করা
        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_id' => $eventId,
                    'action_source' => 'website',
                    'event_source_url' => url()->current(),
                    'user_data' => $userData,
                    'custom_data' => $customData,
                ],
            ],
            'access_token' => $setting->access_token,
        ];

        if (! empty($setting->test_event_code)) {
            $payload['test_event_code'] = $setting->test_event_code;
        }

        // ফেসবুক এপিআই ভার্সন v23.0 তে হিট করা
        $response = Http::post(
            "https://graph.facebook.com/v23.0/{$setting->pixel_id}/events",
            $payload
        );

        return [
            'status' => $response->successful(),
            'response' => $response->json(),
            'body' => $response->body(),
        ];
    }
}
