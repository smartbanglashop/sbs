<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Courier Label</title>

    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .label {
            width: 100mm;
            min-height: 150mm;
            border: 1px solid #000;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 2px solid #000;
        }

        .logo img {
            height: 45px;
        }

        .shop-name {
            font-size: 18px;
            font-weight: bold;
        }

        .merchant {
            font-size: 12px;
        }

        .barcode {
            text-align: center;
            padding: 10px;
            border-bottom: 2px solid #000;
        }

        .barcode-number {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .middle {
            display: flex;
            border-bottom: 2px solid #000;
        }

        .qr {
            width: 35%;
            text-align: center;
            padding: 5px;
            border-right: 2px solid #000;
        }

        .info {
            width: 65%;
            padding: 5px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .customer {
            padding: 8px;
            border-bottom: 2px solid #000;
        }

        .customer table {
            width: 100%;
        }

        .customer td {
            padding: 3px;
            vertical-align: top;
        }

        .cod {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #000;
            margin: 10px;
            padding: 10px 15px;
            font-weight: bold;
        }

        .cod-title {
            font-size: 14px;
            text-transform: uppercase;
        }

        .cod-amount {
            font-size: 28px;
        }

        .footer {
            padding: 8px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
        }

        @media print {

            button {
                display: none;
            }

        }
    </style>

</head>

<body onload="window.print()">

    <button onclick="window.print()">Print</button>

    <div class="label">

        <div class="header">

            <div class="logo">
                <img src="{{asset($generalsetting->white_logo)}}">
            </div>

            <div align="right">

                <div class="shop-name">
                    Smart Bangla Shop
                </div>

                <div class="merchant">
                    Merchant ID : UXRHVA2G
                    {{-- {{ $shipment->merchant_id }} --}}
                </div>

            </div>

        </div>


        <div class="barcode">

            {{-- {!! DNS1D::getBarcodeHTML($shipment->consignment_id,'C128',3.5,40) !!} --}}
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($shipment->consignment_id,'C128',3,70) }}"
                style="width:100%;height:60px;">

            <div class="barcode-number">
                {{ $shipment->consignment_id }}
            </div>

        </div>


        <div class="middle">

            <div class="qr">

                {{-- {!! QrCode::size(120)->generate($shipment->tracking_code) !!} --}}
                {!! QrCode::size(120)->generate($shipment->consignment_id) !!}

            </div>

            <div class="info">

                <div class="row">
                    <span>Invoice</span>
                    <strong>{{ $shipment->invoice }}</strong>
                </div>

                <div class="row">
                    <span>SF-ID</span>
                    <strong>{{ $shipment->consignment_id }}</strong>
                </div>

                <div class="row">
                    <span>Tracking</span>
                    <strong>{{ $shipment->tracking_code }}</strong>
                </div>

                <div class="row">
                    <span>Status</span>
                    <strong>{{ ucfirst($shipment->status) }}</strong>
                </div>

                <div class="row">
                    <span>Delivery</span>
                    <strong>Home</strong>
                </div>

                {{-- <div class="row">
                    <span>Weight</span>
                    <strong>0.5 KG</strong>
                </div> --}}

            </div>

        </div>


        <div class="customer">

            <table>

                <tr>
                    <td width="70">Name</td>
                    <td>{{ $shipment->order->shipping->name ?? '' }}</td>
                </tr>

                <tr>
                    <td>Phone</td>
                    <td>{{ $shipment->order->shipping->phone ?? '' }}</td>
                </tr>

                <tr>
                    <td>Address</td>
                    <td>{{ $shipment->order->shipping->address ?? '' }}</td>
                </tr>

                <tr>
                    <td>Area</td>
                    <td>{{ $shipment->order->shipping->area ?? '' }}</td>
                </tr>

            </table>

        </div>


        <div class="cod">
            <span class="cod-title">CASH ON DELIVERY</span>
            <span class="cod-amount">
                ৳ {{ number_format($shipment->order->amount) }}
            </span>
        </div>


        <div class="footer">

            <div>

                Printed :
                {{ now()->format('d M Y h:i A') }}

            </div>

            <div>

                {{ strtoupper($shipment->courier_name) }}

            </div>

        </div>

    </div>

</body>

</html>