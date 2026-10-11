@extends('frontEnd.layouts.master')
@section('title', $details->name) 
@push('seo')
<meta name="app-url" content="{{ route('product', $details->slug) }}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $details->meta_description }}" />
<meta name="keywords" content="{{ $details->slug }}" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="{{ $details->name }}" />
<meta name="twitter:title" content="{{ $details->name }}" />
<meta name="twitter:description" content="{{ $details->meta_description }}" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="{{ route('product', $details->slug) }}" />
<meta name="twitter:image" content="{{ asset($details->image ? $details->image->image : '') }}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{ $details->name }}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ route('product', $details->slug) }}" />
<meta property="og:image" content="{{ asset($details->image ? $details->image->image : '') }}" />
<meta property="og:description" content="{{ $details->meta_description }}" />
<meta property="og:site_name" content="{{ $details->name }}" />
@endpush

@push('css')
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/zoomsl.css') }}">
<style>
    .main-details-page { background: #ffffff; padding-top: 15px; }
    .details_right { padding: 5px 10px; background: #ffffff; }
    
    .product-title-min { font-size: 1.4rem; font-weight: 700; color: #111; margin-bottom: 8px; line-height: 1.3; }
    .price-box-min { font-size: 1.5rem; font-weight: 800; color: #f70c14; margin-bottom: 12px; }
    .price-box-min del { font-size: 1rem; color: #888; font-weight: 400; margin-right: 8px; }
    
    /* Order Now Button (Green & Pulsing Animation) */
    .btn-minimal-order {
        background-color: #1BAB2D !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1.1rem;
        border-radius: 6px;
        padding: 12px 20px;
        border: none;
        width: 100%;
        display: block;
        text-align: center;
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        animation: pulse-green 2s infinite;
        cursor: pointer;
    }

    /* Add to Cart Button (Red) */
    .btn-minimal-cart {
        background-color: #FF0000 !important; /* Red */
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 1rem;
        border-radius: 6px;
        padding: 11px 20px;
        border: none;
        width: 100%;
        display: block;
        text-align: center;
        box-shadow: 0 3px 6px rgba(217, 4, 41, 0.3);
        cursor: pointer;
    }

    /* Contact Buttons with Hover Effects */
    .btn-minimal-call {
        background-color: #ffffff !important;
        color: #0d6efd !important;
        border: 2px solid #0d6efd !important;
        font-weight: 700;
        border-radius: 6px;
        padding: 10px;
        width: 100%;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-minimal-call:hover { background-color: #0d6efd !important; color: #fff !important; }

    .btn-minimal-wa {
        background-color: #ffffff !important;
        color: #25D366 !important;
        border: 2px solid #25D366 !important;
        font-weight: 700;
        border-radius: 6px;
        padding: 10px;
        width: 100%;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-minimal-wa:hover { background-color: #25D366 !important; color: #fff !important; }

    /* Button Pulse Animation Keyframes */
    @keyframes pulse-green {
        2% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
    }
    
    /* Stylized Shipping Box */
    .shipping-box-styled {
        background: #f8f9fa;
        border: 1px dashed #2b8a3e;
        border-radius: 8px;
        padding: 12px 15px;
        margin-top: 20px;
    }
    .shipping-box-styled h6 {
        font-weight: 700;
        color: #2b8a3e;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .shipping-table-min { width: 100%; font-size: 0.95rem; margin: 0; }
    .shipping-table-min td { padding: 6px 0; border-bottom: 1px solid #e9ecef; color: #444; }
    .shipping-table-min tr:last-child td { border-bottom: none; }

    /* Quantity Box */
    .qty-box-min {
        display: flex;
        align-items: center;
        border: 1px solid #ced4da;
        border-radius: 6px;
        overflow: hidden;
    }
    .qty-box-min button {
        background: #f1f3f5;
        border: none;
        width: 38px;
        height: 42px;
        font-weight: bold;
        font-size: 1.2rem;
        color: #333;
    }
    .qty-box-min input {
        border: none;
        text-align: center;
        width: 100%;
        height: 42px;
        font-weight: 600;
    }

    /* Tabs Layout */
    .desc-nav-min { border-bottom: 2px solid #eee; margin-bottom: 25px; margin-top: 30px; }
    .desc-nav-min ul { display: flex; gap: 20px; list-style: none; padding: 0; margin: 0; }
    .desc-nav-min a { text-decoration: none; color: #666; font-weight: 600; padding-bottom: 10px; display: inline-block; }
    .desc-nav-min a.active { color: #f70c14; border-bottom: 3px solid #f70c14; }

    /* Standalone Product Gallery (no external carousel library needed) */
    .main-image-box {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .main-image-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .thumb-row {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
    }
    .thumb-row .thumb-item {
        flex: 0 0 auto;
        width: 65px;
        height: 65px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        background: #fff;
    }
    .thumb-row .thumb-item.active { border-color: #f70c14; }
    .thumb-row .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="homeproduct main-details-page">
    <div class="container">
        <div class="row">
            <!-- Left: Main Image First, Sub-images Below -->
            <div class="col-md-6 position-relative mb-4">
                @if($details->old_price)
                <div class="product-details-discount-badge">
                    <div class="sale-badge">
                        <div class="sale-badge-inner">
                            <div class="sale-badge-box">
                                <span class="sale-badge-text">
                                    @php $discount=(((($details->old_price)-($details->new_price))*100) / ($details->old_price)) @endphp 
                                    {{ number_format($discount, 0) }}% ছাড়
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Main Product Image (self-contained, no external carousel dependency) -->
                <div class="main-image-box">
                    <img id="mainProductImage"
                         src="{{ asset($details->images->first() ? $details->images->first()->image : ($details->image ? $details->image->image : '')) }}"
                         alt="{{ $details->name }}" />
                </div>

                @if($details->images->count() > 1)
                <div class="thumb-row">
                    @foreach ($details->images as $key => $image)
                        <div class="thumb-item @if($key == 0) active @endif" onclick="document.getElementById('mainProductImage').src='{{ asset($image->image) }}'; document.querySelectorAll('.thumb-item').forEach(function(el){el.classList.remove('active');}); this.classList.add('active');">
                            <img src="{{ asset($image->image) }}" alt="{{ $details->name }}" />
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Right: Product Info & Actions -->
            <div class="col-md-6">
                <div class="details_right">
                    <h1 class="product-title-min">{{ $details->name }}</h1>
                    
                    <div class="price-box-min">
                        @if ($details->old_price)
                            <del>৳{{ $details->old_price }}</del>
                        @endif 
                        ৳{{ $details->new_price }}
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3 text-secondary">
                        <span>কোড: <strong class="text-dark">{{ $details->product_code }}</strong></span>
                        <span>|</span>
                        <span>ব্র্যান্ড: <strong class="text-dark">{{ $details->brand ? $details->brand->name : 'N/A' }}</strong></span>
                    </div>

                    @if($details->note)
                    <div class="alert alert-warning p-2 mb-3 text-dark">
                        <strong>নোট:</strong> {{ $details->note }}
                    </div>
                    @endif

                    <form action="{{ route('cart.store') }}" method="POST" name="formName">
                        @csrf
                        <input type="hidden" name="id" value="{{ $details->id }}" />

                        <!-- Color Selector -->
                        @if ($productcolors->count() > 0)
                        <div class="mb-3">
                            <label class="fw-bold mb-2">কালার নির্বাচন করুন:</label>
                            <div class="d-flex gap-2 wrap">
                                @foreach ($productcolors as $procolor)
                                    <div class="selector-item">
                                        <input type="radio" id="fc-option{{ $procolor->id }}" value="{{ $procolor->color ? $procolor->color->colorName : '' }}" name="product_color" class="selector-item_radio" required />
                                        <label for="fc-option{{ $procolor->id }}" class="btn btn-outline-dark btn-sm">
                                            {{ $procolor->color ? $procolor->color->colorName : '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Size Selector -->
                        @if ($productsizes->count() > 0)
                        <div class="mb-3">
                            <label class="fw-bold mb-2">সাইজ নির্বাচন করুন:</label>
                            <div class="d-flex gap-2 wrap">
                                @foreach ($productsizes as $prosize)
                                    <div class="selector-item">
                                        <input type="radio" id="f-option{{ $prosize->id }}" value="{{ $prosize->size ? $prosize->size->sizeName : '' }}" name="product_size" class="selector-item_radio" required />
                                        <label for="f-option{{ $prosize->id }}" class="btn btn-outline-dark btn-sm">
                                            {{ $prosize->size ? $prosize->size->sizeName : '' }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($details->stock < 1)
                            <div class="alert alert-danger text-center font-weight-bold">স্টক আউট</div>
                        @else
                            <!-- Quantity & Action Buttons -->
                            <div class="row g-2 align-items-center mb-3">
                                <div class="col-4">
                                    <div class="qty-box-min">
                                        <button type="button" class="minus" onclick="var i=this.parentNode.querySelector('input[name=qty]'); var v=parseInt(i.value,10)||1; if(v>1){i.value=v-1;}">-</button>
                                        <input type="text" name="qty" value="1" readonly />
                                        <button type="button" class="plus" onclick="var i=this.parentNode.querySelector('input[name=qty]'); var v=parseInt(i.value,10)||1; i.value=v+1;">+</button>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <input type="submit" class="btn btn-minimal-order" name="order_now" value="অর্ডার করুন" />
                                </div>
                                <div class="col-12 mt-1">
                                    <input type="submit" class="btn btn-minimal-cart" name="add_cart" value="কার্টে যোগ করুন" />
                                </div>
                            </div>
                        @endif

                        <!-- Call & WhatsApp -->
                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <a href="tel:{{ $contact->hotline }}" class="btn-minimal-call">
                                    <i class="fa-solid fa-phone me-1"></i> কল করুন
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="https://wa.me/{{str_replace(['+', ' ', '-'], '', $contact->whatsapp)}}?text={{ urlencode('Hello, I am interested in: ' . $details->name) }}" class="btn-minimal-wa">
                                    <i class="fa-brands fa-whatsapp me-1"></i> হোয়াটসঅ্যাপ
                                </a>
                            </div>
                        </div>

                        <!-- Stylized Courier Charge Box -->
                        <div class="shipping-box-styled">
                            <h6><i class="fa-solid fa-truck-fast"></i> কুরিয়ার ডেলিভারি খরচ</h6>
                            <table class="shipping-table-min">
                                <tbody>
                                    @foreach ($shippingcharge as $value)
                                    <tr>
                                        <td>{{ $value->name }}</td>
                                        <td class="text-end fw-bold" style="color:#2b8a3e;">৳ {{ $value->amount }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs & Detailed Section -->
<div class="container">
    <div class="desc-nav-min">
        <ul>
            <li><a href="#description" class="active">বিস্তারিত</a></li>
            <li><a href="#orderpolicy">অর্ডার পলিসি</a></li>
            <li><a href="#writeReview">রিভিউ ({{ $reviews->count() }})</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div id="description" class="mb-5">
                <div class="lh-lg text-dark">{!! $details->description !!}</div>
            </div>

            <div id="orderpolicy" class="mb-5">
                <h4 class="fw-bold mb-3">Order Policy</h4>
                <div class="lh-lg text-dark">{!! $generalsetting->order_policy !!}</div>
            </div>

            <div id="writeReview" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0">কাস্টমার রিভিউ ({{ $reviews->count() }})</h4>
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        রিভিউ লিখুন
                    </button>
                </div>

                @if ($reviews->count() > 0)
                    @foreach ($reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong class="text-dark">{{ $review->name }}</strong>
                                <small class="text-muted">{{ $review->created_at->format('d-m-Y') }}</small>
                            </div>
                            <div class="text-warning small my-1">
                                {!! str_repeat('<i class="fa-solid fa-star"></i>', $review->ratting) !!}
                            </div>
                            <p class="text-dark m-0 fs-6">{{ $review->review }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">এখনো কোনো রিভিউ দেওয়া হয়নি। প্রথম রিভিউটি আপনিই লিখুন!</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Suggested / Related Products Section -->
    @if(isset($products) && $products->count() > 0)
    <div class="related-products mt-5 mb-5">
        <h4 class="fw-bold mb-4 border-bottom pb-2">সাজেস্টেড প্রোডাক্টস (Suggested Products)</h4>
        <div class="row g-3">
            @foreach($products->take(24) as $value)
            <div class="col-6 col-md-3">
                <div class="product-card border rounded p-2 text-center h-100">
                    <a href="{{ route('product', $value->slug) }}">
                        <img src="{{ asset($value->image ? $value->image->image : '') }}" class="img-fluid rounded mb-2" alt="{{ $value->name }}" style="height: 160px; object-fit: cover;">
                    </a>
                    <h6 class="text-truncate fs-6 mb-1">
                        <a href="{{ route('product', $value->slug) }}" class="text-dark text-decoration-none">{{ $value->name }}</a>
                    </h6>
                    <div class="fw-bold text-danger">৳{{ $value->new_price }}</div>
                    @if($value->old_price)
                        <del class="text-muted small">৳{{ $value->old_price }}</del>
                     @endif
                    <form action="{{ route('cart.store') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="id" value="{{ $value->id }}" />
                        <input type="hidden" name="qty" value="1" />
                        <button type="submit" class="btn btn-sm w-100" style="background-color:#f70c14; color:#fff; font-weight:700; border:none;">
                            কার্টে যোগ করুন
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection