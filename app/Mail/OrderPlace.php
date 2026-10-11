<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlace extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $orderDetails;
    public $shipping;

    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
        
        // সম্পর্কযুক্ত ডেটাগুলো কন্ট্রোলার থেকে অলরেডি লোড হয়ে আসলে সরাসরি রিলেশন থেকে নেওয়া সবচেয়ে নিরাপদ
        $this->customer     = $order->customer ?? null;
        $this->shipping     = $order->shipping ?? null;
        $this->orderDetails = $order->orderdetails ?? collect();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Order Confirmation - ' . config('app.name'))
                    ->view('emails.order_place_for_admin')
                    ->with([
                        'order'        => $this->order,
                        'customer'     => $this->customer,
                        'shipping'     => $this->shipping,
                        'orderDetails' => $this->orderDetails,
                    ]);
    }
}