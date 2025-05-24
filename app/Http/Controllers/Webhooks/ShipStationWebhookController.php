<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\FgpOrder;

class ShipStationWebhookController extends Controller
{
    public function handle(Request $request)
    {
        
        // ✅ Step 1: Token validation
        if ($request->query('token') !== config('services.shipstation.webhook_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $data = $request->all();
        Log::info('ShipStation Webhook Received:', $data);

        // ✅ Step 2: Basic DAta validation
        if (!isset($data['resource_type'], $data['resource_url'], $data['event'], $data['resource_id'])) {
            Log::warning('ShipStation Webhook: Incomplete payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $shipstationOrderId = $data['resource_id'];
        $event = $data['event'];

        // ✅ Step 3: Pull order details from ShipStation API
        $response = Http::withBasicAuth(
            config('services.shipstation.key'),
            config('services.shipstation.secret')
        )->get("https://ssapi.shipstation.com/orders/{$shipstationOrderId}");

        if (!$response->successful()) {
            Log::error("ShipStation API call failed for order ID: {$shipstationOrderId}", ['response' => $response->body()]);
            return response()->json(['error' => 'Failed to fetch order'], 502);
        }

        $orderData = $response->json();

        if (!isset($orderData['orderNumber'])) {
            Log::error("Missing orderNumber in ShipStation data", $orderData);
            return response()->json(['error' => 'Missing order number'], 422);
        }

        // ✅ Step 4: Parse order number
        $fgpOrderId = intval(str_replace('FGP-', '', $orderData['orderNumber']));
        $order = FgpOrder::where('fgp_ordersID', $fgpOrderId)->first();

        if (!$order) {
            Log::warning("No matching FGP Order found for ID: FGP-{$fgpOrderId}");
            return response()->json(['error' => 'Order not found'], 404);
        }

        // ✅ Step 5: Update order details
        $order->update([
            'shipstation_order_id'     => $shipstationOrderId,
            'shipstation_status'       => $orderData['orderStatus'] ?? $order->shipstation_status,
            'shipstation_label_url'    => $orderData['labelData']['labelDownload']['href'] ?? null,
            'tracking_number'          => $orderData['advancedOptions']['customField1'] ?? null,
            'label_created_at'         => now(),
            'shipstation_webhook_event'=> $event,
            'shipstation_raw_response' => json_encode($orderData),
        ]);

        Log::info("FGP Order FGP-{$fgpOrderId} updated via ShipStation webhook.");
        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
