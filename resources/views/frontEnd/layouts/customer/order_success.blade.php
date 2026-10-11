@extends('frontEnd.layouts.master')
@section('title','Order Success')
@section('content')
<section class="customer-section print-container" style="padding: 20px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                
                <!-- ইমেজ এবং h2 মেসেজ (স্বাভাবিক ব্রাউজারে দেখাবে, প্রিন্ট করার সময় ১০০% হাইড থাকবে) -->
                <div class="no-print text-center">
                    <div class="success-img" style="margin-bottom: 15px;">
                        <img src="{{asset('public/frontEnd/images/order-success.png')}}" alt="" style="max-width: 180px; height: auto;">
                    </div>
                    
                    <div class="success-title" style="margin-bottom: 25px; padding: 0 15px;">
                        <h2 style="font-size: 16px; font-weight: 600; color: #2d3748; line-height: 1.6; text-align: center; margin: 0 auto; max-width: 600px;">
                            আপনার অর্ডারটি আমাদের কাছে সফলভাবে পৌঁছেছে, প্রয়োজনে আমাদের একজন প্রতিনিধি আপনার নাম্বারে কল করতে পারেন,অর্ডারটি ভেরিফাই করার জন্য।
                        </h2>
                    </div>
                </div>

                <!-- মূল অর্ডার ইনফরমেশন ফ্রেম -->
                <div class="order-summary-box" style="background: #fdfdfd; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative;">
                    
                    <!-- হেডার এবং ফ্রেমের ভেতরে ছোট প্রিন্ট বাটন -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #28a745; padding-bottom: 8px;">
                        <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: bold;">Order Information</h4>
                        
                        <!-- ছোট আইকনসহ প্রিন্ট বাটন -->
                        <button onclick="window.print();" class="btn btn-sm btn-success no-print" style="background-color: #28a745; border: none; padding: 4px 12px; font-size: 12px; font-weight: 600; border-radius: 4px; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                    
                    <!-- ইনফরমেশন সেকশন: বামে লোগো ও কোম্পানির তথ্য, ডানে কাস্টমারের তথ্য -->
                    <div class="row mb-4" style="font-size: 13px; line-height: 1.5;">
                        <!-- বাম পাশ: লোগো ও শপ ডিটেইলস -->
                        <div class="col-6" style="text-align: left;">
                            <div class="invoice-logo" style="margin-bottom: 10px; display: block !important;">
                                <img src="{{asset('public/uploads/settings/1773953366-new-logo.webp')}}" alt="Smart Bangla Shop" style="max-width: 150px; height: auto; display: block !important; visibility: visible !important;">
                            </div>
                            <p style="margin: 0 0 8px 0; color: #555; font-size: 12px;">Website: www.smartbanglashop.com<br>Hotline: 01917304907<br>Email: support@smartbanglashop.com</p>
                            
                            <p style="margin: 0; color: #666;"><strong>Invoice ID:</strong> #{{$order->invoice_id}}</p>
                            <p style="margin: 0; color: #666;"><strong>Date:</strong> {{$order->created_at->format('d-M-Y')}}</p>
                            <p style="margin: 0; color: #666;"><strong>Payment:</strong> {{ strtoupper($order->payment->payment_method ?? 'COD') }}</p>
                        </div>
                        
                        <!-- ডান পাশ: কাস্টমারের তথ্য -->
                        <div class="col-6 text-end" style="text-align: right; padding-top: 70px;">
                            <p style="margin: 0 0 4px 0; color: #666;"><strong>Shipping To:</strong></p>
                            <p style="margin: 0 0 4px 0; font-weight: bold; color: #333; font-size: 14px;">{{$order->shipping->name ?? ''}}</p>
                            <p style="margin: 0 0 4px 0; color: #666; font-weight: 500;">{{$order->shipping->phone ?? ''}}</p>
                            <p style="margin: 0; color: #666;">{{$order->shipping->address ?? ''}}</p>
                        </div>
                    </div>

                    <h5 style="margin-top: 10px; color: #444; font-size: 14px; font-weight: bold;">Order Summary</h5>
                    <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 13px;">
                        <thead>
                            <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 10px 8px; text-align: left; color: #4a5568;">Product Description</th>
                                <th style="padding: 10px 8px; text-align: right; color: #4a5568; width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $calculated_subtotal = 0; @endphp
                            @foreach($order->orderdetails as $value)
                            @php $calculated_subtotal += ($value->sale_price * $value->qty); @endphp
                            <tr style="border-bottom: 1px solid #edf2f7;">
                                <td style="padding: 10px 8px; color: #2d3748;">
                                    <span style="font-weight: 500;">{{$value->product_name}}</span> 
                                    <strong style="color: #28a745;">x {{$value->qty}}</strong>
                                    @if($value->product_size || $value->product_color)
                                        <br>
                                        <small style="color: #718096; font-size: 11px;">
                                            @if($value->product_size) Size: {{$value->product_size}} @endif 
                                            @if($value->product_color) | Color: {{$value->product_color}} @endif
                                        </small>
                                    @endif
                                </td>
                                <td style="padding: 10px 8px; text-align: right; font-weight: 500; color: #2d3748;">৳{{$value->sale_price * $value->qty}}</td>
                            </tr>
                            @endforeach
                            
                            <!-- হিসাব নিকাশ অংশ -->
                            <tr style="border-top: 1px solid #e2e8f0;">
                                <td style="padding: 6px 8px; text-align: right; color: #4a5568;">Subtotal:</td>
                                <td style="padding: 6px 8px; text-align: right; font-weight: 500;">৳{{$calculated_subtotal}}</td>
                            </tr>
                            <tr>
                                <td style="padding: 6px 8px; text-align: right; color: #4a5568;">Delivery Charge:</td>
                                <td style="padding: 6px 8px; text-align: right; font-weight: 500; color: #e53e3e;">৳{{$order->shipping_charge}}</td>
                            </tr>
                            <tr style="background-color: #f8fafc; font-size: 16px; font-weight: bold; border-top: 2px solid #e2e8f0;">
                                <td style="padding: 10px 8px; text-align: right; color: #1a202c;">Grand Total:</td>
                                <td style="padding: 10px 8px; text-align: right; color: #28a745;">৳{{$calculated_subtotal + $order->shipping_charge}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- প্রিন্ট মিডিয়া স্টাইল (১টি মাত্র নিখুঁত পেজের জন্য সর্বোচ্চ প্রায়োরিটি দেওয়া সিএসএস) -->
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 0mm !important;
    }
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        visibility: hidden;
        background-color: #fff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .no-print {
        display: none !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    /* পুরো পেজের প্যারেন্টদের প্রিন্ট এরর দূর করার ট্রিক */
    .print-container, .print-container .container, .print-container .row {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .order-summary-box, .order-summary-box * {
        visibility: visible !important;
    }
    .order-summary-box {
        position: fixed !important; /* ফিক্সড করে একদম ১ম পেজের টপে পুশ করা হলো */
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        border: none !important;
        box-shadow: none !important;
        padding: 30px !important;
        margin: 0 !important;
        background: #fff !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
    }
    .invoice-logo img {
        display: block !important;
        visibility: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
@endsection

@push('script')
<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>
<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>

<!-- GTM Tracking Data Layer -->
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({ ecommerce: null });
    dataLayer.push({
        'event': 'purchase',
        'user_data': {
            'first_name': '{{ $order->shipping->name ?? "" }}',
            'phone': '{{ $order->shipping->phone ?? "" }}',
            'address': {
                'address': '{{ $order->shipping->address ?? "" }}'
            }
        },
        'ecommerce': {
            'transaction_id': '{{ $order->invoice_id }}',
            'value': {{ $order->amount }},
            'currency': 'BDT',
            'shipping': {{ $order->shipping_charge }},
            'items': [
                @foreach($order->orderdetails as $detail)
                {
                    'item_id': '{{ $detail->product_id }}',
                    'item_name': '{{ $detail->product_name }}',
                    'price': {{ $detail->sale_price }},
                    'quantity': {{ $detail->qty }},
                    'item_variant': '{{ $detail->product_size }}'
                }@if (!$loop->last),@endif
                @endforeach
            ]
        }
    });
</script>
@if(session('lead_event_id'))
<script>
fbq('track', 'Lead', {
    currency: 'BDT',
    value: {{ session('lead_value') }},
    content_ids: @json(session('lead_content_ids')),
    content_type: 'product'
}, {
    eventID: "{{ session('lead_event_id') }}"
});
</script>
@endif

@php
session()->forget([
    'lead_event_id',
    'lead_content_ids',
    'lead_value',
]);
@endphp

@endpush