@extends('frontEnd.layouts.master')

@section('title','Order Track Result')

@section('content')

<section class="py-5" style="background:#f5f7fb; min-height:70vh;">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="card shadow border-0 rounded-4">

                    <div class="card-header bg-primary text-white py-3">

                        <h3 class="mb-0">
                            <i class="fa fa-truck"></i>
                            Order Tracking
                        </h3>

                    </div>

                    <div class="card-body">

                        <div class="row mb-4">

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <small class="text-muted">Invoice ID</small>
                                    <h6 class="mb-0">{{$order->invoice_id}}</h6>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <small class="text-muted">Order Date</small>
                                    <h6 class="mb-0">{{$order->created_at}}</h6>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <small class="text-muted">Order Status</small>

                                    <span class="badge bg-success px-3 py-2">

                                        {{ optional(App\Models\Orderstatus::find($order->order_status))->name }}

                                    </span>

                                </div>
                            </div>

                        </div>

                        @php
                            $orderdetails = App\Models\OrderDetails::where('order_id',$order->id)->get();
                        @endphp

                        <div class="table-responsive">

                            <table class="table table-bordered align-middle">

                                <thead class="table-dark">

                                    <tr>

                                        <th>Product</th>

                                        <th width="120" class="text-center">Qty</th>

                                        <th width="180" class="text-end">Price</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($orderdetails as $product)

                                    <tr>

                                        <td>{{$product->product_name}}</td>

                                        <td class="text-center">

                                            {{$product->qty}}

                                        </td>

                                        <td class="text-end">

                                            {{$product->sale_price * $product->qty}} Tk

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>

                                <tfoot>

                                    <tr>

                                        <th colspan="2" class="text-end">

                                            Delivery Charge

                                        </th>

                                        <th class="text-end">

                                            {{$order->shipping_charge}} Tk

                                        </th>

                                    </tr>

                                    <tr class="table-primary">

                                        <th colspan="2" class="text-end">

                                            Grand Total

                                        </th>

                                        <th class="text-end">

                                            {{$order->amount}} Tk

                                        </th>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection

@push('script')

<script src="{{asset('public/frontEnd/')}}/js/parsley.min.js"></script>

<script src="{{asset('public/frontEnd/')}}/js/form-validation.init.js"></script>

@endpush