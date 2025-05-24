<?php

namespace App\Http\Controllers\Webhooks;

use App\Models\FgpOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ShipStationWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Received ShipStation webhook', ['payload' => $request->all()]);

        $orderNumber = $request->input('orderNumber'); // e.g. "FGP-123"
        $trackingNumber = $request->input('trackingNumber');
        $shipDate = $request->input('shipDate');

        $orderId = str_replace('FGP-', '', $orderNumber);
        $order = FgpOrder::find($orderId);

        if (!$order) {
            Log::warning('Order not found for webhook', ['orderNumber' => $orderNumber]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->update([
            'shipstation_status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'delivered_at' => now(),
            'is_delivered' => true,
        ]);

        return response()->json(['message' => 'Order updated'], 200);
    }
}
