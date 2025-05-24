<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FgpOrder;

class ShipStationWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        $order = FgpOrder::where('order_number', $data['orderNumber'])->first();

        if ($order) {
            $order->shipping_status = $data['shipmentStatus'];
            $order->tracking_number = $data['trackingNumber'] ?? null;
            $order->shipped_at = now();
            $order->save();
        }

        return response()->json(['success' => true]);
    }
}
