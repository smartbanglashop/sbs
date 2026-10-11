<!DOCTYPE html>
<html lang="bn">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $generalsetting->name }}</title>
        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" type="image/x-icon" />
        
        <!-- Font Awesome & Google Fonts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Core CSS -->
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/bootstrap.min.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/animate.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.theme.default.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/owl.carousel.min.css" />
        <link rel="stylesheet" href="{{ asset('public/frontEnd/campaign/css') }}/select2.min.css" />

        @foreach($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '{{{$pixel->code}}}');
          fbq('track', 'PageView');
        </script>
        <noscript>
          <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{{$pixel->code}}}&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endforeach
        
        <meta name="app-url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta name="robots" content="index, follow" />
        <meta name="description" content="{{$campaign_data->description}}" />
        <meta name="keywords" content="{{ $campaign_data->slug }}" />
        
        <meta property="og:title" content="{{$campaign_data->name}}" />
        <meta property="og:type" content="product" />
        <meta property="og:url" content="{{route('campaign',$campaign_data->slug)}}" />
        <meta property="og:image" content="{{asset($campaign_data->image_one)}}" />
        <meta property="og:description" content="{{ $campaign_data->description}}" />

        @foreach($gtm_code as $gtm)
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-{{ $gtm->code }}');</script>
        @endforeach

        <style>
            :root {
                --primary: #16a34a;
                --primary-dark: #0f7c38;
                --primary-light: #dcfce7;
                --accent: #f59e0b;
                --danger: #ef4444;
                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-300: #d1d5db;
                --gray-500: #6b7280;
                --text-primary: #111827;
                --text-secondary: #6b7280;
                --border-color: #e5e7eb;
            }

            body {
                font-family: 'Hind Siliguri', 'Inter', sans-serif;
                background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 60%, #eff6ff 100%);
                color: var(--text-primary);
            }

            /* Header Section & Countdown */
            .top-header-sec {
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
                border-bottom: 3px solid var(--accent);
            }
            
            .counter-card {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 10px;
                padding: 8px 4px;
                text-align: center;
            }
            .counter-card div {
                font-size: 1.4rem;
                font-weight: 700;
                color: #ffffff;
            }
            .counter-card span {
                font-size: 0.75rem;
                color: var(--accent);
                text-transform: uppercase;
            }

            /* Headings */
            .custom-heading-box {
                background: #ffffff;
                border-left: 5px solid var(--primary);
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                padding: 15px 20px;
            }
            .animated-heading {
                font-size: 1.8rem;
                font-weight: 700;
                color: var(--primary);
            }

            .feature-box {
                background: #ffffff;
                border: 1px dashed var(--primary);
                border-radius: 12px;
                padding: 20px;
            }

            /* Buttons & Call To Action With Pulse Effect */
            .btn-pulse-order {
                background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
                color: #ffffff !important;
                font-size: 1.3rem;
                font-weight: 700;
                padding: 14px 28px;
                border-radius: 50px;
                display: inline-block;
                text-decoration: none;
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45);
                animation: pulseAttention 1.8s infinite;
            }

            .btn-pulse-call {
                background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
                color: #ffffff !important;
                font-size: 1.2rem;
                font-weight: 700;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                text-decoration: none;
                box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
                animation: pulseAttention 1.8s infinite;
            }

            .btn-pulse-whatsapp {
                background: linear-gradient(135deg, #16a34a 0%, #0f7c38 100%) !important;
                color: #ffffff !important;
                font-size: 1.2rem;
                font-weight: 700;
                padding: 12px 20px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                text-decoration: none;
                box-shadow: 0 6px 16px rgba(22, 163, 74, 0.4);
                animation: pulseGreen 1.8s infinite;
            }

            @keyframes pulseAttention {
                0% { box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45), 0 0 0 0 rgba(239, 68, 68, 0.55); }
                70% { box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45), 0 0 0 14px rgba(239, 68, 68, 0); }
                100% { box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45), 0 0 0 0 rgba(239, 68, 68, 0); }
            }

            @keyframes pulseGreen {
                0% { box-shadow: 0 8px 20px rgba(22, 163, 74, 0.45), 0 0 0 0 rgba(22, 163, 74, 0.55); }
                70% { box-shadow: 0 8px 20px rgba(22, 163, 74, 0.45), 0 0 0 14px rgba(22, 163, 74, 0); }
                100% { box-shadow: 0 8px 20px rgba(22, 163, 74, 0.45), 0 0 0 0 rgba(22, 163, 74, 0); }
            }

            /* Form & Inputs with Icons */
            .checkout-card {
                background: #ffffff;
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 8px 24px rgba(22, 163, 74, 0.12);
                border: 2px solid var(--primary);
            }
            .checkout-card .card-header {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: #ffffff;
                padding: 12px 20px;
                border-radius: 10px;
                font-weight: 700;
            }

            .field-icon-wrap {
                position: relative;
                display: flex;
                align-items: center;
            }
            .field-icon-wrap .field-icon {
                position: absolute;
                left: 14px;
                color: var(--gray-500);
                z-index: 2;
                font-size: 16px;
            }
            .field-icon-wrap .form-control,
            .field-icon-wrap select.form-control {
                padding-left: 44px;
                height: 48px;
                border: 2px solid var(--gray-300);
                border-radius: 10px;
                background: var(--gray-50);
                font-size: 15px;
            }
            .field-icon-wrap .form-control:focus,
            .field-icon-wrap select.form-control:focus {
                border-color: var(--primary);
                background: #fff;
                box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12);
            }

            .validation-message {
                font-size: 13px;
                margin-top: 4px;
            }
            .validation-message.error { color: var(--danger); }
            .validation-message.success { color: var(--primary); }

            /* Product Selection Cards */
            .product-card {
                border: 2px solid var(--gray-300);
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #fff;
            }
            .product-card.selected {
                border-color: var(--primary) !important;
                box-shadow: 0 0 10px rgba(22, 163, 74, 0.2);
                background-color: var(--primary-light);
            }

            /* Cart Table */
            .cartlist table { width: 100% !important; }
            .cartlist td, .cartlist th { vertical-align: middle !important; padding: 10px 8px !important; }

            .order_place_btn {
                width: 100%;
                height: 52px;
                background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
                color: white;
                font-size: 1.2rem;
                font-weight: 700;
                border: none;
                border-radius: 10px;
                box-shadow: 0 8px 20px rgba(239, 68, 68, 0.45);
                animation: pulseAttention 1.8s infinite;
            }

            @media (max-width: 768px) {
                .animated-heading { font-size: 1.3rem; }
                .btn-pulse-order { font-size: 1.1rem; width: 100%; text-align: center; }
                .checkout-card { padding: 15px; }
            }
        </style>

        {!! $generalsetting->header_code !!}
    </head>

    <body>
        @php
            $subtotal = Cart::instance('shopping')->subtotal();
            $subtotal = str_replace(',', '', $subtotal);
            $subtotal = str_replace('.00', '', $subtotal);
            $shipping = Session::get('shipping') ? Session::get('shipping') : 0;
        @endphp

        <!-- Header Banner & Countdown -->
        <section class="top-header-sec py-3 py-md-4">
            <div class="container">
                <div class="row align-items-center gy-3">
                    <div class="col-md-7">
                        <h3 class="text-light text-center text-md-start m-0 fw-bold fs-4">
                            {!! $campaign_data->top_title_1 !!} 
                            <span class="text-warning d-inline-block">{!! $campaign_data->top_title_2 !!}</span>
                        </h3>
                    </div>
                    <div class="col-md-5">
                        <div class="countdown-container">
                            <div class="row g-2" id="countdown">
                                <div class="col-3">
                                    <div class="counter-card">
                                        <div id="days">00</div>
                                        <span>দিন</span>
                                    </div> 
                                </div>
                                <div class="col-3">
                                    <div class="counter-card">
                                        <div id="hours">00</div>
                                        <span>ঘণ্টা</span>
                                    </div>                                        
                                </div>
                                <div class="col-3">
                                    <div class="counter-card">
                                        <div id="minutes">00</div>
                                        <span>মিনিট</span>
                                    </div>                                    
                                </div>
                                <div class="col-3">
                                    <div class="counter-card">
                                        <div id="seconds">00</div>
                                        <span>সেকেন্ড</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Heading 1 -->
        <section class="py-3 py-md-4">
            <div class="container">
                <div class="custom-heading-box text-center">
                    <h2 class="animated-heading m-0">{!! $campaign_data->heading_1 !!}</h2>
                </div>
            </div>
        </section>

        <!-- Banner Image -->
        @if($campaign_data->banner)
        <section class="pb-3 pb-md-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <img class="img-fluid rounded-3 shadow-sm w-100" src="{{asset($campaign_data->banner)}}" alt="Campaign Banner">
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Features -->
        <section class="pb-3 pb-md-4">
            <div class="container">
                <div class="row gy-3">
                    @if($campaign_data->feature_1)
                    <div class="col-md-6">
                        <div class="feature-box h-100 text-center">
                            <h3 class="m-0 fs-5 fw-bold text-success">{!! $campaign_data->feature_1 !!}</h3>
                        </div>
                    </div>
                    @endif
                    @if($campaign_data->feature_2)
                    <div class="col-md-6">
                        <div class="feature-box h-100 text-center">
                            <h3 class="m-0 fs-5 fw-bold text-success">{!! $campaign_data->feature_2 !!}</h3>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Product Video Section With AutoPlay -->
        @if($campaign_data->video != null)
        <section class="py-4 bg-light">
            <div class="container">
                <div class="row justify-content-center gy-3">
                    <div class="col-md-8 text-center">
                        <h3 class="p-3 bg-dark text-white rounded-3 fw-bold m-0 border-start border-4 border-success">
                            পণ্যের সম্পূর্ণ "ভিডিওটি দেখুন"
                        </h3>
                    </div>
                    <div class="col-md-8">
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow-lg border border-2 border-danger">
                            <iframe src="https://www.youtube.com/embed/{{$campaign_data->video}}?autoplay=1&mute=1&enablejsapi=1" title="{{$campaign_data->banner_title}}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col-md-12 text-center mt-3">
                        <a href="#order_form" class="btn-pulse-order">
                            অর্ডার করতে ক্লিক করুন <i class="fa-solid fa-hand-point-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Contact Support With Pulsing Buttons & Icons -->
        <section class="py-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-9 text-center">
                        <div class="bg-white p-4 rounded-3 shadow-sm border border-success">
                            <h4 class="fw-bold mb-3 text-dark">
                                বিস্তারিত জানতে আমাদের কল করুন: <span class="text-danger">{{$contact->phone}}</span>
                            </h4>
                            <div class="row g-3 justify-content-center my-2">
                                <div class="col-md-5">
                                    <a href="tel:{{$contact->phone}}" class="btn-pulse-call">
                                        <i class="fa-solid fa-phone-flip me-2"></i> কল করুন
                                    </a>
                                </div>
                                <div class="col-md-5">
                                    <a href="https://wa.me/{{$contact->whatsapp}}" class="btn-pulse-whatsapp">
                                        <i class="fa-brands fa-whatsapp me-2"></i> হোয়াটসঅ্যাপ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Gallery Slider -->
        <section class="py-4">
            <div class="container">
                <div class="text-center mb-3">
                    <h3 class="fw-bold text-dark">{{$campaign_data->name}}</h3>
                </div>
                <div class="campro_img_slider owl-carousel rounded-3 overflow-hidden shadow-sm">
                    @if($campaign_data->image_one)
                    <div class="campro_img_item">
                        <img src="{{asset($campaign_data->image_one)}}" class="img-fluid" alt="">
                    </div> 
                    @endif
                    @if($campaign_data->image_two)
                    <div class="campro_img_item">
                        <img src="{{asset($campaign_data->image_two)}}" class="img-fluid" alt="">
                    </div> 
                    @endif
                    @if($campaign_data->image_three)
                    <div class="campro_img_item">
                        <img src="{{asset($campaign_data->image_three)}}" class="img-fluid" alt="">
                    </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Customer Reviews -->
        @if(count($campaign_data->images) > 0)
        <section class="py-4 bg-light">
            <div class="container">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-success">{{$campaign_data->review}}</h3>
                </div>
                <div class="review_slider owl-carousel">
                    @foreach($campaign_data->images as $key=>$value)
                    <div class="review_item rounded-3 overflow-hidden shadow-sm bg-white p-1">
                        <img src="{{asset($value->image)}}" class="img-fluid rounded" alt="Customer Review">
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- ORDER FORM SECTION -->
        <section class="form_sec py-5" id="order_form">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="badge bg-danger fs-6 px-3 py-2 mb-2">সীমিত সময়ের অফার</span>
                    <h2 class="fw-bold text-dark">অফার শেষ হওয়ার আগেই অর্ডার নিশ্চিত করুন</h2>
                    @if($campaign_data->note)
                    <p class="text-muted m-0">{!! $campaign_data->note !!}</p>
                    @endif
                </div>

                <div class="row g-4">
                    <!-- Left Column: Cart & Products -->
                    <div class="col-lg-7">
                        @if($products->count() > 1)
                        <div class="checkout-card mb-4">
                            <div class="card-header mb-3">একটি পণ্য নির্বাচন করুন</div>  
                            <div class="row g-2">
                                @foreach($products as $product)
                                <div class="col-md-4 col-6">
                                    <input type="radio" class="d-none" name="product" id="product_{{ $product->id }}" value="{{ $product->id }}" {{ $loop->first ? 'checked' : '' }} onchange="updateCart('{{ $product->id }}')">
                                    <label for="product_{{ $product->id }}" class="product-card p-2 text-center d-block {{ $loop->first ? 'selected' : '' }}">
                                        <img src="{{ asset($product->image->image) }}" class="img-fluid rounded mb-2" style="height: 90px; object-fit: contain;">
                                        <div class="fw-bold fs-6 text-truncate">{{ $product->name }}</div>
                                        <div class="text-success fw-bold">৳{{ $product->new_price }} <del class="text-muted fs-7">৳{{ $product->old_price }}</del></div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="checkout-card">
                            <div class="card-header mb-3">আপনার অর্ডারকৃত পণ্যসমূহ</div>
                            <div class="table-responsive cartlist">
                                <table class="table table-bordered text-center align-middle m-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>প্রোডাক্ট</th>
                                            <th>পরিমাণ</th>
                                            <th>মূল্য</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(Cart::instance('shopping')->content() as $value)
                                        <tr>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{asset($value->options->image)}}" width="45" class="rounded">
                                                    <div>
                                                        <div class="fw-bold fs-6">{{Str::limit($value->name, 25)}}</div>
                                                        @php $product = App\Models\Product::find($value->id); @endphp
                                                        @if($product && ($product->sizes->isNotEmpty() || $product->colors->isNotEmpty()))
                                                        <div class="d-flex gap-1 mt-1">
                                                            @if($product->sizes->isNotEmpty())
                                                            <select class="form-select form-select-sm cart-size-selector" data-id="{{ $value->rowId }}">
                                                                @foreach($product->sizes as $size)
                                                                <option value="{{ $size->sizeName }}" {{ $size->sizeName == $value->options->product_size ? 'selected' : '' }}>{{ $size->sizeName }}</option>
                                                                @endforeach
                                                            </select>
                                                            @endif

                                                            @if($product->colors->isNotEmpty())
                                                            <select class="form-select form-select-sm cart-color-selector" data-id="{{ $value->rowId }}">
                                                                @foreach($product->colors as $color)
                                                                <option value="{{ $color->colorName }}" {{ $color->colorName == $value->options->product_color ? 'selected' : '' }}>{{ $color->colorName }}</option>
                                                                @endforeach
                                                            </select>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-secondary cart_decrement" data-id="{{$value->rowId}}">-</button>
                                                    <button class="btn btn-light" disabled>{{$value->qty}}</button>
                                                    <button class="btn btn-outline-secondary cart_increment" data-id="{{$value->rowId}}">+</button>
                                                </div>
                                            </td>
                                            <td class="fw-bold">৳{{$value->price*$value->qty}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2" class="text-end pe-3">মোট:</th>
                                            <td class="fw-bold">৳{{$subtotal}}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-end pe-3">ডেলিভারি চার্জ:</th>
                                            <td class="fw-bold">৳{{$shipping}}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="2" class="text-end pe-3 fs-5">সর্বমোট:</th>
                                            <td class="fw-bold fs-5 text-success">৳{{$subtotal+$shipping}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Delivery Form with Icons -->
                    <div class="col-lg-5">
                        <div class="checkout-card">
                            <div class="card-header mb-3">ডেলিভারি তথ্য দিন</div>
                            <form action="{{route('customer.ordersave')}}" method="POST">
                                @csrf
                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">আপনার সম্পূর্ণ নাম <span class="text-danger">*</span></label>
                                    <div class="field-icon-wrap">
                                        <i class="fa-solid fa-user field-icon"></i>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{old('name')}}" placeholder="আপনার নাম লিখুন" required>
                                    </div>
                                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-semibold">মোবাইল নম্বর <span class="text-danger">*</span></label>
                                    <div class="field-icon-wrap">
                                        <i class="fa-solid fa-phone field-icon"></i>
                                        <input type="tel" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{old('phone')}}" placeholder="01xxxxxxxxx" maxlength="11" required>
                                    </div>
                                    <small id="phone_msg" class="validation-message d-block"></small>
                                    @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label fw-semibold">সম্পূর্ণ ঠিকানা <span class="text-danger">*</span></label>
                                    <div class="field-icon-wrap">
                                        <i class="fa-solid fa-location-dot field-icon"></i>
                                        <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{old('address')}}" placeholder="জেলা, থানা, গ্রাম/এলাকার নাম" required>
                                    </div>
                                    @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <!-- Delivery Area Select -->
                                <div class="mb-4">
                                    <label for="area" class="form-label fw-semibold">ডেলিভারি এরিয়া সিলেক্ট করুন <span class="text-danger">*</span></label>
                                    <div class="field-icon-wrap">
                                        <i class="fa-solid fa-truck field-icon"></i>
                                        <select id="area" class="form-select @error('area') is-invalid @enderror" name="area" required>
                                            @foreach($shippingcharge as $key=>$value)
                                            <option value="{{$value->id}}">{{$value->name}} (৳{{$value->amount}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button class="order_place_btn" type="submit" id="submit_btn">
                                    <i class="fa-solid fa-circle-check me-2"></i> অর্ডার কনফার্ম করুন
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Scripts -->
        <script src="{{ asset('public/frontEnd/campaign/js') }}/jquery-2.1.4.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/bootstrap.min.js"></script>
        <script src="{{ asset('public/frontEnd/campaign/js') }}/owl.carousel.min.js"></script>

        <script>
            $(document).ready(function () {
                $(".campro_img_slider").owlCarousel({
                    margin: 10,
                    loop: true,
                    dots: true,
                    autoplay: true,
                    items: 1
                });

                $('.review_slider').owlCarousel({   
                    dots: true,
                    autoplay: true,
                    loop: true,
                    margin: 15,
                    responsive: {
                        0: { items: 1 },
                        480: { items: 2 },
                        768: { items: 3 }
                    }
                });

                $("#area").on("change", function () {
                    var id = $(this).val();
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('shipping.charge')}}",
                        dataType: "html",
                        success: function(response){
                            $('.cartlist').html(response);
                        }
                    });
                });

                // Phone Validation (11 Digits Realtime Check)
                $('#phone').on('input', function() {
                    let phone = $(this).val();
                    let msg = $('#phone_msg');
                    let submitBtn = $('#submit_btn');

                    if (phone.length === 0) {
                        msg.removeClass('success error').text('');
                        submitBtn.prop('disabled', false);
                    } else if (!/^01[0-9]*$/.test(phone)) {
                        msg.addClass('error').removeClass('success').text('⚠️ নম্বর অবশ্যই 01 দিয়ে শুরু হতে হবে');
                        submitBtn.prop('disabled', true);
                    } else if (phone.length < 11) {
                        msg.addClass('error').removeClass('success').text('⚠️ ১১ ডিজিটের সম্পূর্ণ নম্বর দিন');
                        submitBtn.prop('disabled', true);
                    } else if (phone.length === 11) {
                        msg.addClass('success').removeClass('error').text('✓ সঠিক নম্বর');
                        submitBtn.prop('disabled', false);
                    }
                });

                // LocalStorage AutoSave
                $('#name').val(localStorage.getItem('campaign_name') || '');
                $('#phone').val(localStorage.getItem('campaign_phone') || '');
                $('#address').val(localStorage.getItem('campaign_address') || '');

                $('#name').on('blur', function() { localStorage.setItem('campaign_name', $(this).val()); });
                $('#phone').on('blur', function() { localStorage.setItem('campaign_phone', $(this).val()); });
                $('#address').on('blur', function() { localStorage.setItem('campaign_address', $(this).val()); });
            });

            // Cart Increment/Decrement
            $(document).on("click", ".cart_increment, .cart_decrement", function () {
                var id = $(this).data("id");
                var route = $(this).hasClass("cart_increment") ? "{{route('cart.increment')}}" : "{{route('cart.decrement')}}";
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: route,
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        }
                    });
                }
            });

            function updateCart(productId) {
                $('.product-card').removeClass('selected');
                $('#product_' + productId).next('label').addClass('selected');
                if (productId) {
                    $.ajax({
                        type: "GET",
                        data: { id: productId },
                        url: "{{route('cart.changeProduct')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                            }
                        }
                    });
                }
            }

            // Countdown Timer
            const deadline = new Date("{{ $campaign_data->deadline }}").getTime();
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = deadline - now;

                if (distance > 0) {
                    document.getElementById("days").innerHTML = Math.floor(distance / (1000 * 60 * 60 * 24));
                    document.getElementById("hours").innerHTML = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    document.getElementById("minutes").innerHTML = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    document.getElementById("seconds").innerHTML = Math.floor((distance % (1000 * 60)) / 1000);
                } else {
                    clearInterval(x);
                    document.getElementById("countdown").innerHTML = "<div class='col-12 text-white fw-bold'>অফারের সময় শেষ!</div>";
                }
            }, 1000);
        </script>
        {!! $generalsetting->footer_code !!}
    </body>
</html>