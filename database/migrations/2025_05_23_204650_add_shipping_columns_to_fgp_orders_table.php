<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\FgpOrder;
use Illuminate\Support\Facades\Log;

class ShipStationService
{
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->apiKey = config('services.shipstation.key');
        $this->apiSecret = config('services.shipstation.secret');
    }

    public function sendOrder(FgpOrder $order): void
    {
        $lineItems = $order->items->map(function ($item) {
            return [
                'lineItemKey' => 'item-' . $item->fgp_item_id,
                'name' => $item->fgpItem->name ?? 'Pop Item',
                'quantity' => $item->unit_number,
                'unitPrice' => $item->unit_cost,
            ];
        })->toArray();

        $payload = [
            'orderNumber' => 'FGP-' . $order->id,
            'orderDate' => now()->toIso8601String(),
            'orderStatus' => $order->shipstation_status ?? 'awaiting_shipment',
            'shipTo' => [
                'name'        => $order->ship_to_name,
                'street1'     => $order->ship_to_address1,
                'street2'     => $order->ship_to_address2,
                'city'        => $order->ship_to_city,
                'state'       => $order->ship_to_state,
                'postalCode'  => $order->ship_to_zip,
                'country'     => $order->ship_to_country ?? 'US',
                'phone'       => $order->ship_to_phone,
            ],
            'shippingMethod' => $order->ship_method ?? 'Standard',
            'items' => $lineItems,
        ];

        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->withOptions(['verify' => false])
            ->post('https://ssapi.shipstation.com/orders/createorder', $payload);

        if ($response->failed()) {
            Log::error('ShipStation API failed', ['response' => $response->body()]);
        } else {
            Log::info('Order sent to ShipStation', ['order_id' => $order->id]);
        }
    }
}
