<?php

namespace App\Http\Controllers;

use App\Models\CourierShipment;
use Illuminate\Http\Request;

class CourierShipmentController extends Controller
{
    public function print(CourierShipment $shipment)
{
    $shipment->load('order.shipping');

    if ($shipment->courier_name == 'steadfast') {

        return view(
            'backEnd.courier.steadfast.print',
            compact('shipment')
        );
    }

    abort(404);
}
}
