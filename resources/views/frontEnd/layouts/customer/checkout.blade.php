@extends('frontEnd.layouts.master')
@section('title', 'Customer Checkout')
@push('css')
    <style>
        * {
            box-sizing: border-box;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');

        :root {
            --primary: #16a34a;
            --primary-dark: #0f7c38;
            --primary-light: #dcfce7;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --info: #0ea5e9;
            --info-light: #e0f2fe;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            /* ০৩ - ডিফল্ট ইনপুট বর্ডার কালার (ব্লু) */
            --border-blue: #3b82f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 60%, #eff6ff 100%);
            color: var(--text-primary);
        }

        .chheckout-section {
            padding: 40px 0;
            /* ০২ - ফুটারের আগে বড় গ্যাপের কারণ ছিল min-height:100vh, তাই সরানো হলো */
        }

        /* ===== PROGRESS STEPPER ===== */
        .progress-stepper {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 30px;
            margin-bottom: 50px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .step-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
        }

        .step.completed .step-number {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        .step.pending .step-number {
            background: var(--gray-100);
            color: var(--gray-400);
            border: 2px solid var(--border-color);
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step.active .step-label {
            color: var(--primary);
        }

        .step.completed .step-label {
            color: var(--success);
        }

        .step.pending .step-label {
            color: var(--gray-400);
        }

        /* ===== MAIN LAYOUT ===== */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .checkout-form-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.12);
            border: 2px solid var(--primary);
        }

        .checkout-summary-section {
            position: sticky;
            top: 30px;
        }

        /* ===== FORM SECTIONS ===== */
        .form-section {
            margin-bottom: 40px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--gray-100);
        }

        .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .section-icon.icon-info {
            background: var(--info-light);
            color: var(--info);
        }

        .section-icon svg {
            width: 18px;
            height: 18px;
        }

        /* ===== FORM FIELDS ===== */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            gap: 4px;
        }

        .required {
            color: var(--danger);
            font-weight: 700;
        }

        /* ০৩ - ফিল্ড বর্ডার এখন ডায়নামিক (শিফটিং) ব্লু গ্র্যাডিয়েন্ট, ফোকাস করলে সবুজ */
       .form-control,
select.form-control {
    height: 48px;
    padding: 12px 16px;
    border: 2px solid transparent !important;
    border-radius: 10px;
    font-size: 15px;
    font-family: inherit;
    color: var(--text-primary);
    transition: all 0.3s ease;
    background:
        linear-gradient(var(--gray-50), var(--gray-50)) padding-box,
        linear-gradient(120deg, #3b82f6, #06b6d4, #2563eb, #3b82f6) border-box !important;
    background-size: 100% 100%, 300% 300% !important;
    animation: dynamicBorder 6s ease infinite;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

.form-control:focus,
select.form-control:focus {
    outline: none;
    border-color: transparent !important;
    background:
        linear-gradient(white, white) padding-box,
        linear-gradient(120deg, var(--primary), var(--primary-dark)) border-box !important;
    animation: none;
    box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12) !important;
}
        @keyframes dynamicBorder {
            0% {
                background-position: 0% 0%, 0% 50%;
            }
            50% {
                background-position: 0% 0%, 100% 50%;
            }
            100% {
                background-position: 0% 0%, 0% 50%;
            }
        }

        textarea.form-control {
            height: auto;
            min-height: 100px;
            resize: vertical;
            padding: 12px 16px;
        }

        /* ===== SELECT DROPDOWN ===== */
        .select-wrapper {
            position: relative;
        }

        select.form-control {
            appearance: none;
            padding-right: 40px;
            cursor: pointer;
        }

        /* ===== ICON-PREFIXED FIELDS (নাম, ফোন, ঠিকানা, নোট, জেলা, থানা) ===== */
        .field-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon-wrap .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .field-icon-wrap .field-icon svg {
            width: 19px;
            height: 19px;
        }

        .field-icon-wrap .form-control {
            padding-left: 46px;
        }

        .field-icon-wrap.select-wrapper select.form-control {
            padding-left: 46px;
        }

        /* টেক্সটএরিয়া (অর্ডার নোট) হলে আইকন উপরের দিকে সারিবদ্ধ থাকবে */
        .field-icon-wrap.field-icon-textarea {
            align-items: flex-start;
        }

        .field-icon-wrap.field-icon-textarea .field-icon {
            top: 16px;
            transform: none;
        }

        .field-icon-wrap:focus-within .field-icon {
            color: var(--primary);
        }

        /* ===== SELECT2 কাস্টমাইজেশন — ফিল্ড আইকনের সাথে মানানসই করতে ===== */
        .field-icon-wrap.select-wrapper .select2-container {
            width: 100% !important;
        }

        /* ০৩ - সিলেক্ট২ বর্ডারও এখন ডায়নামিক ব্লু গ্র্যাডিয়েন্ট, ফর্ম ফিল্ডের সাথে মিলিয়ে */
        .field-icon-wrap.select-wrapper .select2-container .select2-selection--single {
            height: 48px !important;
            border: 2px solid transparent !important;
            border-radius: 10px !important;
            background:
                linear-gradient(var(--gray-50), var(--gray-50)) padding-box,
                linear-gradient(120deg, #3b82f6, #06b6d4, #2563eb, #3b82f6) border-box;
            background-size: 100% 100%, 300% 300%;
            animation: dynamicBorder 6s ease infinite;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
        }

        .field-icon-wrap.select-wrapper .select2-container .select2-selection__rendered {
            padding-left: 46px !important;
            padding-right: 30px;
            line-height: 44px !important;
            color: var(--text-primary) !important;
            font-size: 15px;
        }

        .field-icon-wrap.select-wrapper .select2-container--open .select2-selection--single,
        .field-icon-wrap.select-wrapper .select2-container--focus .select2-selection--single {
            border-color: transparent !important;
            background:
                linear-gradient(white, white) padding-box,
                linear-gradient(120deg, var(--primary), var(--primary-dark)) border-box;
            animation: none;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12);
        }

        .field-icon-wrap.select-wrapper .select2-selection__arrow {
            height: 46px !important;
        }

        /* ===== VALIDATION MESSAGES ===== */
        .validation-message {
            font-size: 13px;
            margin-top: 6px;
            display: none;
        }

        .validation-message.error {
            color: var(--danger);
            display: block;
        }

        .validation-message.success {
            color: var(--success);
            display: block;
        }

        /* ===== ALERT BOX ===== */
        /* এই বেস রুলটা ফাইল থেকে হারিয়ে গিয়েছিল বলেই বক্স/বর্ডার ছাড়া প্লেইন টেক্সট
           হিসেবে দেখাচ্ছিল (শুধু মোবাইল মিডিয়া কোয়েরিতে override ছিল, বেস স্টাইল ছিল না)।
           এখানে বেস স্টাইলটা ফিরিয়ে আনা হলো। */
        .alert-box {
            background: linear-gradient(135deg, var(--accent-light) 0%, #fff7ed 100%);
            border: 2px solid var(--warning);
            border-radius: 12px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .alert-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            font-size: 18px;
        }

        .alert-text {
            font-size: 13px;
            color: #92400e;
            font-weight: 500;
            line-height: 1.4;
        }

        /* ===== PAYMENT METHOD ===== */
        /* ০৪ - "পেমেন্ট পদ্ধতি" সেকশন উপরে তোলা / গ্যাপ কমানো */
        #payment-section {
            margin-top: -20px;
        }

        #payment-section .section-title {
            margin-bottom: 12px;
            padding-bottom: 8px;
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 2px solid var(--primary);
            border-radius: 10px;
            cursor: default;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--primary-light) 0%, #ffffff 100%);
        }

        .payment-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
        }

        .payment-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3);
            overflow: hidden;
        }

        .payment-icon img,
        .payment-icon svg {
            width: 24px;
            height: 24px;
        }

        .payment-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .payment-title {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 14px;
        }

        .payment-subtitle {
            font-size: 11px;
            color: var(--text-secondary);
        }

        /* ===== SUBMIT BUTTON ===== */
        .submit-btn-wrapper {
            margin-top: 32px;
        }

        .submit-btn {
            width: 100%;
            height: 56px;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45);
            position: relative;
            overflow: hidden;
            transform-origin: center;
            animation: pulseAttention 1.6s infinite !important;
        }

        /* বাটনের উপর দিয়ে চকচকে আলো বয়ে যাওয়ার শাইন ইফেক্ট */
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.55), transparent);
            transform: skewX(-25deg);
            animation: shineSweep 2.2s infinite;
        }

        .submit-btn:hover:not(:disabled) {
            box-shadow: 0 14px 30px rgba(239, 68, 68, 0.6);
        }

        .submit-btn:active:not(:disabled) {
            transform: scale(0.98);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            animation: none !important;
        }

        .submit-btn:disabled::before {
            animation: none !important;
        }

        .submit-btn svg {
            width: 20px;
            height: 20px;
        }

        /* জোরালো গ্লো + স্কেল পালস */
        @keyframes pulseAttention {
            0% {
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45), 0 0 0 0 rgba(239, 68, 68, 0.6);
            }
            50% {
                transform: scale(1.045);
                box-shadow: 0 10px 26px rgba(239, 68, 68, 0.55), 0 0 0 16px rgba(239, 68, 68, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45), 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        @keyframes shineSweep {
            0% {
                left: -75%;
            }
            60% {
                left: 130%;
            }
            100% {
                left: 130%;
            }
        }

        /* ===== ORDER SUMMARY ===== */
        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .summary-card.highlight {
            border: 2px solid var(--primary);
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.12);
        }

        .summary-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--border-color);
            max-height: 300px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: var(--gray-50);
            border-radius: 10px;
            align-items: flex-start;
            border: 1px solid var(--gray-100);
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            background: white;
            border: 1px solid var(--border-color);
        }

        .cart-item-content {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .cart-item-meta {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .cart-item-price {
            font-weight: 700;
            color: var(--primary);
            font-size: 14px;
        }

        .cart-item-qty {
            display: flex;
            align-items: center;
            gap: 6px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            font-size: 12px;
        }

        .qty-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-weight: 700;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            color: var(--primary);
            transform: scale(1.2);
        }

        .cart-item-remove {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--danger);
            font-size: 18px;
            transition: all 0.2s;
        }

        .cart-item-remove:hover {
            transform: scale(1.2);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 2px solid var(--border-color);
            padding-top: 16px;
            margin-top: 16px;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .summary-row-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .summary-row-value {
            color: var(--text-primary);
            font-weight: 600;
        }

        .taka {
            color: var(--text-secondary);
            font-size: 12px;
            margin-right: 4px;
        }

        /* ===== DELIVERY TIME BADGE ===== */
        .delivery-times {
            background: linear-gradient(135deg, var(--success-light) 0%, #ecfdf5 100%);
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 13px;
            color: #047857;
            font-weight: 600;
            line-height: 1.6;
        }

        /* ===== CHARACTER COUNTER ===== */
        .char-counter {
            color: var(--gray-400);
            margin-top: 4px;
            font-size: 12px;
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .checkout-container {
                display: flex;
                flex-direction: column;
                gap: 24px;
            }

            /* মোবাইলে অর্ডার বিবরণ সবার আগে দেখানো হবে */
            .checkout-summary-section {
                order: -1;
            }

            .checkout-form-section {
                padding: 20px;
            }

            .checkout-summary-section {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .progress-stepper {
                gap: 16px;
                margin-bottom: 28px;
            }

            .step-number {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .step-label {
                font-size: 10px;
            }

            .section-title {
                font-size: 16px;
                margin-bottom: 16px;
            }

            .chheckout-section {
                padding: 20px 0;
            }

            .summary-items {
                max-height: 200px;
            }

            .alert-box {
                margin-bottom: 12px;
                padding: 8px 12px;
            }

            .form-section {
                margin-bottom: 24px;
            }
        }

        @media (max-width: 480px) {
            .checkout-form-section {
                padding: 14px;
                border-radius: 12px;
            }

            .summary-card {
                padding: 16px;
            }

            .form-control,
            select.form-control {
                height: 44px;
                font-size: 14px;
            }

            .submit-btn {
                height: 48px;
                font-size: 15px;
            }

            /* স্টেপার আইকনগুলো পাশাপাশি থাকবে, একটার নিচে একটা না */
            .progress-stepper {
                flex-direction: row;
                gap: 12px;
                align-items: flex-start;
            }

            .alert-box {
                margin-bottom: 10px;
                padding: 8px 10px;
            }

            .alert-text {
                font-size: 12px;
            }

            .payment-option {
                padding: 8px 10px;
            }

            .payment-icon {
                width: 34px;
                height: 34px;
            }

            .payment-icon img,
            .payment-icon svg {
                width: 20px;
                height: 20px;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkout-form-section,
        .summary-card {
            animation: slideInUp 0.5s ease-out;
        }

        /* ===== SCROLLBAR STYLING ===== */
        .summary-items::-webkit-scrollbar {
            width: 6px;
        }

        .summary-items::-webkit-scrollbar-track {
            background: var(--gray-100);
            border-radius: 10px;
        }

        .summary-items::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }

        .summary-items::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* ===== UTILITY ===== */
        .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .container {
            padding-left: 15px;
            padding-right: 15px;
        }

        .text-muted {
            color: var(--gray-400);
        }
    </style>
@endpush

@section('content')
    <section class="chheckout-section">
        @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
        @endphp

        <div class="container">
            <!-- PROGRESS STEPPER -->
            <div class="progress-stepper">
                <div class="step completed">
                    <div class="step-number">✓</div>
                    <div class="step-label">শপিং কার্ট</div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-label">চেকআউট</div>
                </div>
                <div class="step pending">
                    <div class="step-number">3</div>
                    <div class="step-label">সম্পন্ন</div>
                </div>
            </div>

            <!-- MAIN CHECKOUT CONTAINER -->
            <div class="checkout-container">
                <!-- RIGHT SIDE - ORDER SUMMARY (মোবাইলে প্রথমে দেখাবে) -->
                <div class="checkout-summary-section">
                    <!-- ITEMS SUMMARY -->
                    <div class="summary-card highlight">
                        <div class="summary-title">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 20px; height: 20px; color: var(--primary);">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-1.1-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                            </svg>
                            অর্ডার বিবরণ
                        </div>

                        <div class="summary-items">
                            @foreach (Cart::instance('shopping')->content() as $value)
                                <div class="cart-item">
                                    <img src="{{ asset($value->options->image) }}" alt="{{ $value->name }}"
                                        class="cart-item-image">
                                    <div class="cart-item-content">
                                        <div class="cart-item-name">{{ Str::limit($value->name, 25) }}</div>
                                        @if ($value->options->product_size)
                                            <div class="cart-item-meta">সাইজ: {{ $value->options->product_size }}</div>
                                        @endif
                                        @if ($value->options->product_color)
                                            <div class="cart-item-meta">রঙ: {{ $value->options->product_color }}</div>
                                        @endif
                                        <div class="cart-item-price">
                                            <span class="taka">৳</span>{{ number_format($value->price) }}
                                        </div>
                                    </div>
                                    <div class="cart-item-qty">
                                        <button type="button" class="qty-btn cart_decrement"
                                            data-id="{{ $value->rowId }}">−</button>
                                        <span>{{ $value->qty }}</span>
                                        <button type="button" class="qty-btn cart_increment"
                                            data-id="{{ $value->rowId }}">+</button>
                                    </div>
                                    <button type="button" class="cart-item-remove cart_remove"
                                        data-id="{{ $value->rowId }}" title="মুছে ফেলুন">×</button>
                                </div>
                            @endforeach
                        </div>

                        <!-- PRICING SUMMARY -->
                        <div>
                            <div class="summary-row">
                                <span class="summary-row-label">পণ্যের মূল্য</span>
                                <span class="summary-row-value">
                                    <span class="taka">৳</span><span id="net_total">{{ number_format($subtotal) }}</span>
                                </span>
                            </div>

                            <div class="summary-row">
                                <span class="summary-row-label">ডেলিভারি চার্জ</span>
                                <span class="summary-row-value">
                                    <span class="taka">৳</span><span id="cart_shipping_cost">0</span>
                                </span>
                            </div>

                            <div class="summary-row total">
                                <span>মোট বিল</span>
                                <span>
                                    <span class="taka">৳</span><span id="grand_total">{{ number_format($subtotal) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- DELIVERY TIME INFO -->
                    <div class="summary-card">
                        <div class="delivery-times">
                            <strong>⏱️ ডেলিভারি সময়:</strong><br>
                            <strong>ঢাকা:</strong> ১-২ কর্মদিবস<br>
                            <strong>অন্যান্য এলাকা:</strong> ২-৫ কর্মদিবস
                        </div>
                    </div>

                    <!-- HELP TEXT -->
                    <div class="summary-card" style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--info-light) 100%); border-color: var(--primary);">
                        <p style="margin: 0; color: var(--primary-dark); font-size: 13px; line-height: 1.6;">
                            <strong>💡 টিপ:</strong> আপনার অর্ডার নিশ্চিত করার পর আমরা তাৎক্ষণিকভাবে নিশ্চিতকরণ বার্তা পাঠাবো আপনার নাম্বারে।
                        </p>
                    </div>
                </div>

                <!-- LEFT SIDE - FORM -->
                <div class="checkout-form-section">
                    <!-- ALERT BOX -->
                    <div class="alert-box">
                        <div class="alert-icon">⚠️</div>
                        <div class="alert-text">
                            অর্ডার নিশ্চিত করার আগে আপনার ঠিকানা এবং পণ্যের বিবরণ পুনরায় যাচাই করুন।
                        </div>
                    </div>

                    <form action="{{ route('customer.ordersave') }}" method="POST" data-parsley-validate="">
                        @csrf
                        <input type="hidden" id="base_subtotal" value="{{ $subtotal }}">

                        <!-- DELIVERY INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title">
                                <div class="section-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
                                    </svg>
                                </div>
                                ডেলিভারি তথ্য
                            </div>

                            <!-- NAME FIELD -->
                            <div class="form-row full">
                                <div class="form-group">
                                    <label class="form-label">
                                        আপনার সম্পূর্ণ নাম <span class="required">*</span>
                                    </label>
                                    <div class="field-icon-wrap">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21a8 8 0 1 0-16 0" />
                                                <circle cx="12" cy="7" r="4" />
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="আপনার নাম লিখুন" maxlength="15" required>
                                    </div>
                                    <small class="char-counter"><span id="name_count">0</span>/15 ক্যারেক্টার</small>
                                </div>
                            </div>

                            <!-- PHONE FIELD -->
                            <div class="form-row full">
                                <div class="form-group">
                                    <label class="form-label">
                                        যোগাযোগের ফোন নম্বর <span class="required">*</span>
                                    </label>
                                    <div class="field-icon-wrap">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
                                            </svg>
                                        </span>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="01xxxxxxxxx" maxlength="11"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                                    </div>
                                    <small id="phone_msg" class="validation-message"></small>
                                </div>
                            </div>

                            <!-- DISTRICT & THANA -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">জেলা <span class="required">*</span></label>
                                    <div class="select-wrapper field-icon-wrap">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                        </span>
                                        <select name="shipping_district_id" id="shipping_district_id"
                                            class="form-control select2" required>
                                            <option value="">জেলা নির্বাচন করুন</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}">
                                                    {{ $district->name_en }} - {{ $district->name_bn }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">থানা <span class="required">*</span></label>
                                    <div class="select-wrapper field-icon-wrap">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                        </span>
                                        <select name="thana_id" id="thana_id" class="form-control select2" required>
                                            <option value="">আগে জেলা নির্বাচন করুন</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- ADDRESS FIELD -->
                            <div class="form-row full">
                                <div class="form-group">
                                    <label class="form-label">
                                        বিস্তারিত ডেলিভারি ঠিকানা <span class="required">*</span>
                                    </label>
                                    <div class="field-icon-wrap">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M3 9.5 12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" />
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" id="address" name="address"
                                            placeholder="বাসা/ফ্ল্যাট নম্বর, এলাকার নাম, রোড নম্বর ইত্যাদি"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- ORDER NOTE -->
                            <div class="form-row full">
                                <div class="form-group">
                                    <label class="form-label">অর্ডার নোট <span class="text-muted">(ঐচ্ছিক)</span></label>
                                    <div class="field-icon-wrap field-icon-textarea">
                                        <span class="field-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path
                                                    d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" />
                                                <path d="M14 3v6h6" />
                                                <path d="M9 13h6" />
                                                <path d="M9 17h4" />
                                            </svg>
                                        </span>
                                        <textarea name="order_note" id="order_note" class="form-control" maxlength="200"
                                            placeholder="ডেলিভারি সংক্রান্ত কোনো বিশেষ নির্দেশনা থাকলে এখানে লিখুন..."></textarea>
                                    </div>
                                    <small class="char-counter"><span id="note_count">0</span>/200 ক্যারেক্টার</small>
                                </div>
                            </div>
                        </div>

                        <!-- PAYMENT METHOD SECTION -->
                        <div class="form-section" id="payment-section">
                            <div class="section-title">
                                <div class="section-icon icon-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2" />
                                        <path d="M1 10h22" />
                                    </svg>
                                </div>
                                পেমেন্ট পদ্ধতি
                            </div>

                            <div class="payment-methods">
                                <!-- Cash on Delivery (পেমেন্ট পদ্ধতি) -->
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="Cash On Delivery"
                                        checked required>
                                    <div class="payment-icon">
                                        <img src="{{ asset('public/frontEnd/images/cash-on-delivery-icon.png') }}" alt="ক্যাশ অন ডেলিভারি">
                                    </div>
                                    <div class="payment-text">
                                        <div class="payment-title">ক্যাশ অন ডেলিভারি</div>
                                        <div class="payment-subtitle">পণ্য হাতে পেয়ে টাকা পরিশোধ করুন</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="submit-btn-wrapper">
                            <button class="submit-btn order_place" type="submit" id="submit_btn"
                                onclick="return submitOrder(this);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                অর্ডার নিশ্চিত করুন
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('script')
    <script src="{{ asset('public/frontEnd/') }}/js/select2.min.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/parsley.min.js"></script>
    <script src="{{ asset('public/frontEnd/') }}/js/form-validation.init.js"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2({
                width: '100%',
                allowClear: false
            });
        });
    </script>

    <!-- জেলা/থানা সার্চ বক্সে গাইড টেক্সট (placeholder) দেখানো -->
    <script>
        $(document).ready(function() {
            $(document).on('select2:open', function(e) {
                let fieldId = e.target.id;
                let placeholderText = 'এখানে লিখে খুঁজুন';

                if (fieldId === 'shipping_district_id') {
                    placeholderText = 'এখানে জেলার নাম লিখে খুঁজতে পারেন';
                } else if (fieldId === 'thana_id') {
                    placeholderText = 'এখানে থানার নাম লিখে খুঁজতে পারেন';
                }

                setTimeout(function() {
                    document.querySelectorAll('.select2-container--open .select2-search__field')
                        .forEach(function(input) {
                            input.setAttribute('placeholder', placeholderText);
                        });
                }, 0);
            });
        });
    </script>

    <!-- ফোন নম্বর ভ্যালিডেশন -->
    <script>
        $(document).ready(function() {
            let submitBtn = $('#submit_btn');

            $('#phone').on('input', function() {
                let phone = $(this).val();
                let msg = $('#phone_msg');

                if (phone.length == 0) {
                    msg.removeClass('success error');
                    submitBtn.prop('disabled', false);
                } else if (!/^01[0-9]*$/.test(phone)) {
                    msg.addClass('error').removeClass('success')
                        .text('⚠️ নম্বর অবশ্যই 01 দিয়ে শুরু হতে হবে');
                    submitBtn.prop('disabled', true);
                } else if (phone.length < 11) {
                    msg.addClass('error').removeClass('success')
                        .text('⚠️ ১১ ডিজিটের সম্পূর্ণ নম্বর দিন');
                    submitBtn.prop('disabled', true);
                } else if (phone.length == 11) {
                    msg.addClass('success').removeClass('error')
                        .text('✓ সঠিক নম্বর');
                    submitBtn.prop('disabled', false);
                }
            });
        });
    </script>

    <!-- জেলা এবং থানা লোড করা -->
    <!-- ০৫ ফিক্স: থানা AJAX থেকে নতুন <option> বসানোর পর select2 কে
         এই পরিবর্তনের কথা জানানো হচ্ছিল না, তাই ড্রপডাউন খুললেও থানার
         লিস্ট আপডেট/রিফ্রেশ হতো না (দেখে মনে হতো থানা লোডই হয়নি)।
         এখন .trigger('change') কল করে select2 কে রিফ্রেশ করা হচ্ছে,
         পাশাপাশি লোডিং/এরর স্টেট এবং ডিসেবল হ্যান্ডলিং যোগ করা হলো। -->
    <script>
        $(document).ready(function() {
            $('#shipping_district_id').on('change', function() {
                var district_id = $(this).val();
                var $thana = $('#thana_id');

                if (!district_id) {
                    $thana.html('<option value="">আগে জেলা নির্বাচন করুন</option>').trigger('change');
                    return;
                }

                // লোডিং অবস্থায় থানা ড্রপডাউন সাময়িকভাবে ডিসেবল ও লোডিং টেক্সট দেখানো
                $thana.prop('disabled', true)
                    .html('<option value="">লোড হচ্ছে...</option>')
                    .trigger('change');

                $.ajax({
                    url: "{{ url('get-thana') }}/" + district_id,
                    type: "GET",
                    success: function(data) {
                        $thana.prop('disabled', false).html(data);
                        // select2 কে জানানো হচ্ছে যে অপশন বদলেছে, নাহলে UI পুরনো
                        // অবস্থাতেই রয়ে যায় এবং থানা লোড হয়েছে বলে মনে হয় না
                        $thana.val('').trigger('change');
                        updateShippingCharge();
                    },
                    error: function() {
                        $thana.prop('disabled', false)
                            .html('<option value="">থানা লোড করতে সমস্যা হয়েছে, আবার চেষ্টা করুন</option>')
                            .trigger('change');
                    }
                });
            });

            $('#thana_id').on('change', function() {
                updateShippingCharge();
            });
        });
    </script>

    <!-- শিপিং চার্জ আপডেট করা -->
    <script>
        function updateShippingCharge() {
            let district = $('#shipping_district_id option:selected')
                .text()
                .split('-')[0]
                .trim()
                .toLowerCase();

            let thana = $('#thana_id option:selected').data('name') || '';
            let shipping = 120;

            if (district == 'dhaka') {
                let outsideDhaka = [
                    'savar', 'ashulia', 'dohar', 'hemayetpur', 'keraniganj'
                ];

                if (outsideDhaka.includes(thana.toLowerCase())) {
                    shipping = 110;
                } else {
                    shipping = 80;
                }
            }

            let subtotal = parseFloat($('#net_total').text().replace(/[^\d]/g, '')) || 0;

            $('#cart_shipping_cost').text(shipping);
            $('#grand_total').text(subtotal + shipping);
        }
    </script>

   <!-- অর্ডার সাবমিট করা -->
    <script>
        function submitOrder(btn) {
            var form = btn.closest('form');

            if (form.checkValidity()) {
                setTimeout(function() {
                    btn.disabled = true;
                    // স্পিনার এবং 'প্রসেসিং...' লেখা একত্রে যুক্ত করা হলো
                    btn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block; vertical-align: middle;"></span>
                        প্রসেসিং...
                    `;
                }, 1);

                return true;
            } else {
                return true;
            }
        }
    </script>

    <!-- কার্ট আইটেম রিয়েল-টাইম আপডেট -->
    <!-- ০৬ ফিক্স: ব্যাকএন্ড রুট (cart.remove / cart.update) 'id' প্যারামিটার
         পড়ে, কিন্তু আগে শুধু 'rowId' পাঠানো হতো বলে সার্ভারে আইডি NULL যেত এবং
         পণ্য মোছা/আপডেট হতো না। এখন 'id' ও 'rowId' দুটোই পাঠানো হচ্ছে (backward
         compatible), সাথে ডাবল-ক্লিকে দুইবার রিকোয়েস্ট যাওয়া ঠেকাতে বাটন
         সাময়িকভাবে ডিসেবল করা হচ্ছে এবং কার্ট খালি হলে পেজ রিলোড করা হচ্ছে। -->
    <script>
    $(document).ready(function() {
        function recalcTotals() {
            let subtotal = 0;
            $('.cart-item').each(function() {
                let price = parseFloat($(this).find('.cart-item-price').text().replace(/[^\d.]/g, '')) || 0;
                let qty = parseInt($(this).find('.cart-item-qty span').text()) || 0;
                subtotal += price * qty;
            });
            $('#net_total').text(subtotal.toLocaleString());
            $('#base_subtotal').val(subtotal);
            let shipping = parseFloat($('#cart_shipping_cost').text()) || 0;
            $('#grand_total').text((subtotal + shipping).toLocaleString());

            // কার্ট খালি হয়ে গেলে চেকআউট পেজে আর কিছু করার নেই, পেজ রিলোড করে
            // ব্যাকএন্ডের "কার্ট খালি" স্টেট/রিডাইরেক্ট অনুযায়ী দেখানো হবে
            if ($('.cart-item').length === 0) {
                location.reload();
            }
        }

        // কোয়ান্টিটি বৃদ্ধি
        $(document).on('click', '.cart_increment', function() {
            let $btn = $(this);
            if ($btn.prop('disabled')) return;
            let rowId = $btn.data('id');
            let $qtySpan = $btn.siblings('span');
            updateCartItem(rowId, 'increment', $qtySpan, $btn);
        });

        // কোয়ান্টিটি হ্রাস
        $(document).on('click', '.cart_decrement', function() {
            let $btn = $(this);
            if ($btn.prop('disabled')) return;
            let rowId = $btn.data('id');
            let $qtySpan = $btn.siblings('span');
            updateCartItem(rowId, 'decrement', $qtySpan, $btn);
        });

        // কার্ট আইটেম অপসারণ
        $(document).on('click', '.cart_remove', function() {
            let $btn = $(this);
            if ($btn.prop('disabled')) return;

            let rowId = $btn.data('id');
            let $item = $btn.closest('.cart-item');

            if (confirm('এই পণ্যটি সরাতে কি আপনি নিশ্চিত?')) {
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('cart.remove') }}",
                    type: 'POST',
                    data: {
                        id: rowId,
                        rowId: rowId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $item.remove();
                        recalcTotals();
                    },
                    error: function() {
                        $btn.prop('disabled', false);
                        alert('কিছু সমস্যা হয়েছে। পুনরায় চেষ্টা করুন।');
                    }
                });
            }
        });

        function updateCartItem(rowId, action, $qtySpan, $btn) {
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('cart.update') }}",
                type: 'POST',
                data: {
                    id: rowId,
                    rowId: rowId,
                    action: action,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    let qty = parseInt($qtySpan.text());
                    if (action == 'increment') {
                        qty = qty + 1;
                    } else if (action == 'decrement' && qty > 1) {
                        qty = qty - 1;
                    }
                    $qtySpan.text(qty);
                    recalcTotals();
                    $btn.prop('disabled', false);
                },
                error: function() {
                    $btn.prop('disabled', false);
                    alert('কিছু সমস্যা হয়েছে।');
                }
            });
        }
    });
</script>

    <!-- অর্ডার নোট ক্যারেক্টার কাউন্টার -->
    <script>
        $(document).ready(function() {
            $('#order_note').on('input', function() {
                let count = $(this).val().length;
                $('#note_count').text(count);
            });
        });
    </script>

    <!-- নাম ফিল্ড ক্যারেক্টার কাউন্টার -->
    <script>
        $(document).ready(function() {
            $('#name').on('input', function() {
                let count = $(this).val().length;
                $('#name_count').text(count);
            });
        });
    </script>

    <!-- রিয়েল-টাইম কাস্টমার ডাটা সংরক্ষণ (AutoSave) -->
    <script>
        $(document).ready(function() {
            // লোকাল স্টোরেজ থেকে ডাটা লোড করা
            function loadSavedData() {
                $('#name').val(localStorage.getItem('checkout_name') || '');
                $('#phone').val(localStorage.getItem('checkout_phone') || '');
                $('#address').val(localStorage.getItem('checkout_address') || '');
                $('#order_note').val(localStorage.getItem('checkout_note') || '');
            }

            // প্রাথমিক লোড
            loadSavedData();

            // ইনপুট পরিবর্তনে সংরক্ষণ
            $('#name').on('blur', function() {
                localStorage.setItem('checkout_name', $(this).val());
                saveToServer();
            });

            $('#phone').on('blur', function() {
                localStorage.setItem('checkout_phone', $(this).val());
                saveToServer();
            });

            $('#address').on('blur', function() {
                localStorage.setItem('checkout_address', $(this).val());
                saveToServer();
            });

            $('#order_note').on('blur', function() {
                localStorage.setItem('checkout_note', $(this).val());
            });

            // সার্ভারে সংরক্ষণ করা
            function saveToServer() {
                let name = $('#name').val();
                let phone = $('#phone').val();
                let address = $('#address').val();

                if (name || phone || address) {
                    $.ajax({
                        url: "{{ route('guest.customer.session') }}",
                        type: "GET",
                        data: {
                            is_live_track: 1,
                            guest_name: name,
                            guest_phone: phone,
                            guest_address: address
                        }
                    });
                }
            }

            // ফর্ম রিসেট হলে লোকাল স্টোরেজ পরিষ্কার করা
            $('form').on('reset', function() {
                localStorage.removeItem('checkout_name');
                localStorage.removeItem('checkout_phone');
                localStorage.removeItem('checkout_address');
                localStorage.removeItem('checkout_note');
                $('#note_count').text('0');
                $('#name_count').text('0');
            });
        });
    </script>

    <!-- Google Analytics Events -->
    <script type="text/javascript">
        dataLayer.push({
            ecommerce: null
        });
        dataLayer.push({
            event: "view_cart",
            ecommerce: {
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                        {
                            item_name: "{{ $cartInfo->name }}",
                            item_id: "{{ $cartInfo->id }}",
                            price: "{{ $cartInfo->price }}",
                            item_brand: "{{ $cartInfo->options->brand ?? '' }}",
                            item_category: "{{ $cartInfo->options->category ?? '' }}",
                            currency: "BDT",
                            quantity: {{ $cartInfo->qty ?? 0 }}
                        },
                    @endforeach
                ]
            }
        });
    </script>

    <script type="text/javascript">
        dataLayer.push({
            ecommerce: null
        });
        dataLayer.push({
            event: "begin_checkout",
            ecommerce: {
                currency: "BDT",
                value: {{ $subtotal }},
                items: [
                    @foreach (Cart::instance('shopping')->content() as $cartInfo)
                        {
                            item_name: "{{ $cartInfo->name }}",
                            item_id: "{{ $cartInfo->id }}",
                            price: {{ $cartInfo->price }},
                            currency: "BDT",
                            quantity: {{ $cartInfo->qty ?? 0 }}
                        },
                    @endforeach
                ]
            }
        });
    </script>

   {{-- =========================================================
         কুরিয়ার/ফ্রড হিস্ট্রি চেক পপআপ — বিকাশ অগ্রিম পেমেন্ট কনফার্মেশন
         ✅ আপডেটেড - কালারড বক্স, নতুন মেসেজ, ছোট লোগো, মোবাইল ফ্রেন্ডলি ইনপুট
    ========================================================== --}}
    @if (session('fraud_detected') && session('pending_order_id'))
        <div id="custom-fraud-modal"
            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 99999; padding: 20px;">
            <div
                style="background: linear-gradient(135deg, #fff 0%, #f9fafb 100%); padding: 25px; border-radius: 16px; text-align: center; max-width: 420px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); border: 2px solid #f59e0b;">
                
                <!-- লোগো - ছোট করে উপরে উঠানো -->
                <img src="https://smartbanglashop.com/public/uploads/settings/1773953366-new-logo.webp" alt="Smart Bangla Shop"
                    style="width: 100px; height: 30px; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;">


             <h4 style="color: #dc2626; margin: 0 0 14px 0; font-family: sans-serif; font-size: 16px; font-weight: 700;">⚠️ অর্ডার নোটিশ</h4>

                <!-- অর্ডার নোটিশ বক্স (বর্ডার ও ব্যাকগ্রাউন্ড কালার পরিবর্তিত) -->
                <div style="background: linear-gradient(135deg, #e0f2fe 0%, #cffafe 100%); border: 2px solid #ef4444; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px; text-align: left;">
                    <p style="color: #991b1b; font-size: 13px; line-height: 1.5; margin: 0; font-weight: 600;">
                        {{ session('fraud_detected') }}
                    </p>
                </div>

                <!-- বিকাশ নম্বর বক্স -->
                <div style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%); border: 2px dashed #ec4899; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px; text-align: center;">
                    <p style="margin: 0 0 6px 0; font-size: 12px; color: #831843; font-weight: 600;">বিকাশ/নগদ পার্সোনাল (Send Money):</p>
                    <p style="margin: 0; font-size: 18px; font-weight: 700; color: #d97706; letter-spacing: 1px;">01671456311</p>
                </div>

                <!-- দ্বিতীয় বক্স - অতিরিক্ত মেসেজ (নতুন) -->
                <div style="background: linear-gradient(135deg, #e0f2fe 0%, #cffafe 100%); border: 2px solid #0ea5e9; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; text-align: left;">
                    <p style="color: #0369a1; font-size: 13px; line-height: 1.5; margin: 0; font-weight: 500;">
                        <strong>💡 দয়াকরে প্রতারণা করবেন না:</strong> আমরা সকল লেনদেন যাচাই করি। সঠিক তথ্য প্রদান করুন এবং নিরাপদ কেনাকাটা উপভোগ করুন।
                    </p>
                </div>

<!-- Transaction ID ইনপুট (এখানে কাস্টম কালার ও ব্যাকগ্রাউন্ড দেওয়া হয়েছে) -->
                <form action="{{ route('fraud.payment.confirm') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ session('pending_order_id') }}">
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; color: #374151; font-weight: 600; margin-bottom: 6px; text-align: left;">
                            বিকাশ/নগদ Transaction ID (TrxID)
                        </label>
                        <input type="text" name="transaction_id" placeholder="উদাহরণ: 8N7A6D5C" required
                            style="width: 100%; height: 48px; border: 2px solid #3b82f6; background-color: #fff; border-radius: 10px; padding: 0 14px; font-size: 14px; outline: none; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; box-sizing: border-box; transition: all 0.3s ease;"
                            onfocus="this.style.borderColor='#16a34a'; this.style.boxShadow='0 0 0 4px rgba(22,163,74,0.1)';"
                            onblur="this.style.borderColor='#3b82f6'; this.style.boxShadow='none';">
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
    <!-- পালস এনিমেশনের জন্য স্টাইল -->
<style>
    @keyframes attentionGlow {
        0% {
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.8), 0 4px 12px rgba(22,163,74,0.3);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(22, 163, 74, 0), 0 6px 20px rgba(22,163,74,0.6);
            transform: scale(1.02);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0), 0 4px 12px rgba(22,163,74,0.3);
            transform: scale(1);
        }
    }
    .pulse-btn {
        animation: attentionGlow 1s infinite ease-in-out;
    }
    .pulse-btn:hover {
        transform: translateY(-2px) scale(1.02) !important;
    }
</style>

<!-- লেখার উপরে সাদা শেডো (text-shadow) সহ অর্ডার নিশ্চিত করুন বাটন -->
<button type="submit" class="pulse-btn"
    style="background: linear-gradient(135deg, #16a34a 0%, #0f7c38 100%); border: 2px solid #86efac; padding: 14px; border-radius: 10px; color: #fff; cursor: pointer; font-weight: 700; font-size: 15px; text-shadow: 0 2px 4px rgba(255, 255, 255, 0.6); transition: all 0.3s ease;">
    ✓ অর্ডার নিশ্চিত করুন
</button>
                        
                        <!-- বাতিল করুন বাটনটি এখন আকর্ষণীয় লাল কালার ব্যাকগ্রাউন্ড ও বর্ডারযুক্ত করা হলো -->
                        <button type="button" onclick="document.getElementById('custom-fraud-modal').style.display='none';"
                            style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #ef4444; padding: 8px; border-radius: 10px; color: #991b1b; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.3s ease;"
                            onmouseover="this.style.background='linear-gradient(135deg, #fecaca 0%, #fca5a5 100%)'; this.style.boxShadow='0 4px 12px rgba(239,68,68,0.2)';"
                            onmouseout="this.style.background='linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)'; this.style.boxShadow='none';">
                            ✕ বাতিল করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endpush