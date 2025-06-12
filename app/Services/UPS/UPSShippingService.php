<?php

namespace App\Services\UPS;

use Illuminate\Support\Facades\Http;
use App\Models\FgpOrder;
use App\Models\FgpOrderDetail;
use App\Models\UpsShipment;

class UPSShippingService
{
    protected string $token;

    public function __construct()
    {
        $this->authenticate();
    }

    protected function authenticate()
    {
        $res = Http::asForm()->post(config('ups.base_url') . '/security/v1/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => config('ups.client_id'),
            'client_secret' => config('ups.client_secret'),
        ]);

        $this->token = $res->json('access_token');
    }

    public function validateAddress(array $address)
    {
        $res = Http::withToken($this->token)
            ->post(config('ups.base_url') . '/api/addressvalidation/v1/validate', [
                'address' => $address,
            ]);

        return $res->json();
    }

    public function createShipment(FgpOrder $order)
    {
        $items = $order->orderDetails()->with('flavor')->get();
        $weight = $items->sum(fn($item) => $item->unit_number * 0.5); // Estimate: 0.5 lbs per unit

        $payload = [
            "ShipmentRequest" => [
                "Shipment" => [
                    "Shipper" => [
                        "Name" => "Frios HQ",
                        "ShipperNumber" => config('ups.account_number'),
                        "Address" => [
                            "AddressLine" => ["123 HQ St"],
                            "City" => "Birmingham",
                            "StateProvinceCode" => "AL",
                            "PostalCode" => "35203",
                            "CountryCode" => "US"
                        ]
                    ],
                    "ShipTo" => [
                        "Name" => $order->ship_to_name,
                        "Address" => [
                            "AddressLine" => [$order->ship_to_address1, $order->ship_to_address2],
                            "City" => $order->ship_to_city,
                            "StateProvinceCode" => $order->ship_to_state,
                            "PostalCode" => $order->ship_to_zip,
                            "CountryCode" => $order->ship_to_country,
                        ]
                    ],
                    "Service" => [
                        "Code" => "03"
                    ],
                    "Package" => [
                        [
                            "PackagingType" => ["Code" => "02"],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => ["Code" => "LBS"],
                                "Weight" => number_format($weight, 1)
                            ]
                        ]
                    ]
                ],
                "LabelSpecification" => [
                    "LabelImageFormat" => ["Code" => "ZPL"]
                ]
            ]
        ];

        $res = Http::withToken($this->token)
            ->post(config('ups.base_url') . '/api/shipments/v1/ship', $payload);

        $data = $res->json();

        if ($res->failed()) {
            throw new \Exception('UPS API failed: ' . $res->body());
        }

        $labelImage = base64_decode(data_get($data, 'ShipmentResponse.ShipmentResults.PackageResults.ShippingLabel.GraphicImage'));
        $tracking = data_get($data, 'ShipmentResponse.ShipmentResults.PackageResults.TrackingNumber');
        $shipmentId = data_get($data, 'ShipmentResponse.ShipmentResults.ShipmentIdentificationNumber');

        $file = "labels/ups_label_{$order->fgp_ordersID}.zpl";
        \Storage::disk('local')->put($file, $labelImage);

        UpsShipment::create([
            'fgp_ordersID' => $order->fgp_ordersID,
            'shipment_id' => $shipmentId,
            'tracking_number' => $tracking,
            'label_format' => 'ZPL',
            'label_file_path' => $file,
            'raw_response' => $data,
        ]);

        return $tracking;
    }
}
