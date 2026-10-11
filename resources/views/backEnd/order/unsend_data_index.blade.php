@extends('backEnd.layouts.master')

@section('title', 'Unsend Meta Purchase Data')

@section('content')

    <div class="container-fluid">


        <div class="row">

            <div class="col-12">

                <div class="page-title-box">

                    <h4 class="page-title">
                        Unsend Meta Purchase Data
                    </h4>

                </div>

            </div>

        </div>


        <div class="row order_page">

            <div class="col-12">

                <div class="card">

                    <div class="card-body">




                        <div class="row mb-3">

                            <div class="col-sm-8">

                                <ul class="action2-btn">

                                    <li>

                                        <button type="button" id="sendSelectedMeta" class="btn rounded-pill btn-success">

                                            <i class="fe-send"></i>

                                            Send Selected Meta

                                        </button>

                                    </li>

                                </ul>

                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th style="width:2%">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input checkall">
                                                </label>
                                            </div>
                                        </th>

                                        <th style="width:4%">
                                            SL
                                        </th>

                                        <th style="width:12%">
                                            Action
                                        </th>

                                        <th style="width:8%">
                                            Invoice
                                        </th>

                                        <th style="width:10%">
                                            Date
                                        </th>

                                        <th style="width:12%">
                                            Name
                                        </th>

                                        <th style="width:10%">
                                            Phone
                                        </th>

                                        <th style="width:8%">
                                            Amount
                                        </th>

                                        <th style="width:8%">
                                            Status
                                        </th>

                                        <th style="width:10%">
                                            Courier Status
                                        </th>

                                        <th style="width:8%">
                                            Meta
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @forelse($show_data as $key => $value)
                                        <tr>

                                            <td>

                                                <input type="checkbox" class="checkbox" value="{{ $value->id }}">

                                            </td>
                                            <td>

                                                {{ $loop->index+1 }}

                                            </td>

                                            <td>

                                                <div class="button-list custom-btn-list">


                                                  
                                                    {{-- Meta Details --}}
                                                    <button type="button" class="btn btn-sm btn-info btn-meta-details"
                                                        data-invoice="{{ $value->invoice_id }}"
                                                        data-fbp="{{ $value->fbp }}" data-fbc="{{ $value->fbc }}"
                                                        data-ip="{{ $value->client_ip }}"
                                                        data-useragent="{{ $value->user_agent }}"
                                                        data-name="{{ optional($value->shipping)->name }}"
                                                        data-phone="{{ optional($value->shipping)->phone }}"
                                                        data-address="{{ optional($value->shipping)->address }}"
                                                        data-amount="{{ $value->amount }}" title="Meta Details">

                                                        <i class="fe-info"></i>
                                                        {{-- <i class="fe-eye"></i> --}}

                                                    </button>


                                                    {{-- Single Send --}}
                                                    <button type="button" class="btn btn-sm btn-success btn-send-meta"
                                                        data-id="{{ $value->id }}" title="Send Purchase to Meta">

                                                        <i class="fe-send"></i>

                                                    </button>

                                                </div>

                                            </td>
                                            <td>

                                                <strong>
                                                    {{ $value->invoice_id }}
                                                </strong>

                                            </td>
                                            <td>

                                                {{ date('d-m-Y', strtotime($value->updated_at)) }}

                                                <br>

                                                <small class="text-muted">

                                                    {{ date('h:i:s a', strtotime($value->updated_at)) }}

                                                </small>

                                            </td>
                                            <td>
                                                <strong>
                                                    {{ optional($value->shipping)->name }}
                                                </strong>


                                                @if (optional($value->shipping)->address)
                                                    <p class="mb-0 text-muted small">

                                                        {{ $value->shipping->address }}

                                                    </p>
                                                @endif

                                            </td>
                                            <td>

                                                {{ optional($value->shipping)->phone }}

                                            </td>
                                            <td>

                                                <strong>

                                                    ৳{{ number_format($value->amount, 2) }}

                                                </strong>

                                            </td>
                                            <td>

                                                {{ optional($value->status)->name }}

                                            </td>
                                            <td>

                                                {{ $value->steadfast_status }}

                                            </td>
                                            <td>

                                                @if ($value->meta_purchase_sent == 1)
                                                    <span class="badge bg-success">
                                                        Sent
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-black">
                                                        Unsent
                                                    </span>
                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="11" class="text-center py-5">

                                                <i class="fe-check-circle" style="font-size:45px;color:#28a745;">
                                                </i>

                                                <h5 class="mt-3">
                                                    No Unsent Meta Purchase Data
                                                </h5>

                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>
                        {{-- @if ($show_data->hasPages())
                            <div class="custom-paginate mt-3">
                                {{ $show_data->links('pagination::bootstrap-4') }}
                            </div>
                        @endif --}}


                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="modal fade" id="metaDetailsModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="fe-facebook"></i>

                        Meta Purchase Data

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>

                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="card border">

                                <div class="card-header">

                                    <strong>
                                        Order Information
                                    </strong>

                                </div>

                                <div class="card-body">

                                    <table class="table table-bordered mb-0">

                                        <tr>

                                            <th width="35%">
                                                Invoice
                                            </th>

                                            <td id="meta_invoice">
                                                -
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Name
                                            </th>

                                            <td id="meta_name">
                                                -
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Phone
                                            </th>

                                            <td id="meta_phone">
                                                -
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Amount
                                            </th>

                                            <td>

                                                ৳<span id="meta_amount">
                                                    -
                                                </span>

                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Address
                                            </th>

                                            <td id="meta_address">
                                                -
                                            </td>

                                        </tr>

                                    </table>

                                </div>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- META DATA --}}
                        {{-- ================================================= --}}

                        <div class="col-md-6">

                            <div class="card border">

                                <div class="card-header">

                                    <strong>
                                        Meta / Browser Data
                                    </strong>

                                </div>

                                <div class="card-body">


                                    {{-- FBP --}}
                                    <div class="mb-3">

                                        <label class="form-label">

                                            <strong>
                                                FBP
                                            </strong>

                                        </label>

                                        <textarea id="meta_fbp" class="form-control" rows="2" readonly></textarea>

                                    </div>


                                    {{-- FBC --}}
                                    <div class="mb-3">

                                        <label class="form-label">

                                            <strong>
                                                FBC
                                            </strong>

                                        </label>

                                        <textarea id="meta_fbc" class="form-control" rows="4" readonly></textarea>

                                    </div>


                                    {{-- IP --}}
                                    <div class="mb-3">

                                        <label class="form-label">

                                            <strong>
                                                Client IP
                                            </strong>

                                        </label>

                                        <input type="text" id="meta_ip" class="form-control" readonly>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- USER AGENT --}}
                        {{-- ================================================= --}}

                        <div class="col-12">

                            <div class="card border">

                                <div class="card-header">

                                    <strong>
                                        User Agent
                                    </strong>

                                </div>

                                <div class="card-body">

                                    <textarea id="meta_useragent" class="form-control" rows="4" readonly></textarea>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {


            /*
            |--------------------------------------------------------------------------
            | CHECK ALL
            |--------------------------------------------------------------------------
            */

            $(".checkall").on("change", function() {

                $(".checkbox").prop(
                    "checked",
                    $(this).is(":checked")
                );

            });


            /*
            |--------------------------------------------------------------------------
            | META DETAILS
            |--------------------------------------------------------------------------
            */

            $(document).on(
                "click",
                ".btn-meta-details",
                function() {

                    let button = $(this);


                    $("#meta_invoice").text(
                        button.attr("data-invoice") || "N/A"
                    );


                    $("#meta_name").text(
                        button.attr("data-name") || "N/A"
                    );


                    $("#meta_phone").text(
                        button.attr("data-phone") || "N/A"
                    );


                    $("#meta_amount").text(
                        button.attr("data-amount") || "0"
                    );


                    $("#meta_address").text(
                        button.attr("data-address") || "N/A"
                    );


                    $("#meta_fbp").val(
                        button.attr("data-fbp") || "N/A"
                    );


                    $("#meta_fbc").val(
                        button.attr("data-fbc") || "N/A"
                    );


                    $("#meta_ip").val(
                        button.attr("data-ip") || "N/A"
                    );


                    $("#meta_useragent").val(
                        button.attr("data-useragent") || "N/A"
                    );


                    $("#metaDetailsModal").modal("show");

                }
            );


            $(document).on(
                "click",
                ".btn-send-meta",
                function() {

                    let button = $(this);

                    let orderId = button.data("id");


                    if (!confirm(
                            "Are you sure you want to send this Purchase data to Meta?"
                        )) {

                        return;

                    }


                    button.prop("disabled", true);


                    button.html(
                        '<i class="fa fa-spinner fa-spin"></i>'
                    );


                    $.ajax({

                        type: "POST",

                        url: "{{ route('admin.order.send_meta_purchase', ':id') }}"
                            .replace(":id", orderId),

                        data: {

                            _token: "{{ csrf_token() }}"

                        },


                        success: function(res) {


                            if (res.status) {

                                toastr.success(
                                    res.message
                                );


                                setTimeout(function() {

                                    location.reload();

                                }, 700);


                            } else {

                                toastr.error(
                                    res.message || "Send failed."
                                );


                                button.prop(
                                    "disabled",
                                    false
                                );


                                button.html(
                                    '<i class="fe-send"></i>'
                                );

                            }

                        },


                        error: function(xhr) {

                            let message =
                                "Meta API request failed.";


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.message
                            ) {

                                message =
                                    xhr.responseJSON.message;

                            }


                            toastr.error(message);


                            button.prop(
                                "disabled",
                                false
                            );


                            button.html(
                                '<i class="fe-send"></i>'
                            );

                        }

                    });

                }
            );


            /*
            |--------------------------------------------------------------------------
            | SEND SELECTED META
            |--------------------------------------------------------------------------
            */

            $(document).on(
                "click",
                "#sendSelectedMeta",
                function() {

                    let button = $(this);


                    /*
                    |--------------------------------------------------------------------------
                    | Get Checked IDs
                    |--------------------------------------------------------------------------
                    */

                    let orderIds = $(".checkbox:checked")
                        .map(function() {

                            return $(this).val();

                        })
                        .get();


                    /*
                    |--------------------------------------------------------------------------
                    | Nothing Selected
                    |--------------------------------------------------------------------------
                    */

                    if (orderIds.length === 0) {

                        toastr.warning(
                            "Please select at least one order."
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Confirmation
                    |--------------------------------------------------------------------------
                    */

                    if (!confirm(
                            "Send " +
                            orderIds.length +
                            " selected order(s) to Meta?"
                        )) {

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Loading
                    |--------------------------------------------------------------------------
                    */

                    button.prop(
                        "disabled",
                        true
                    );


                    button.html(
                        '<i class="fa fa-spinner fa-spin"></i> Sending...'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | AJAX
                    |--------------------------------------------------------------------------
                    */

                    $.ajax({

                        type: "POST",

                        url: "{{ route('admin.order.send_selected_meta_purchase') }}",

                        data: {

                            _token: "{{ csrf_token() }}",

                            order_ids: orderIds

                        },


                        success: function(res) {


                            if (res.status) {

                                toastr.success(
                                    res.message
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | Failed Orders
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    res.failed_count &&
                                    res.failed_count > 0
                                ) {

                                    console.log(
                                        "Failed Orders:",
                                        res.failed_orders
                                    );


                                    toastr.warning(
                                        res.failed_count +
                                        " order(s) failed."
                                    );

                                }


                                setTimeout(function() {

                                    location.reload();

                                }, 1200);


                            } else {

                                toastr.warning(
                                    res.message
                                );


                                button.prop(
                                    "disabled",
                                    false
                                );


                                button.html(
                                    '<i class="fe-send"></i> Send Selected Meta'
                                );

                            }

                        },


                        error: function(xhr) {

                            let message =
                                "Something went wrong.";


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.message
                            ) {

                                message =
                                    xhr.responseJSON.message;

                            }


                            toastr.error(message);


                            button.prop(
                                "disabled",
                                false
                            );


                            button.html(
                                '<i class="fe-send"></i> Send Selected Meta'
                            );

                        }

                    });

                }
            );

        });
    </script>

@endsection
