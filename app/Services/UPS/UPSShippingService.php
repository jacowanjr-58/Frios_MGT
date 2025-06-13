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
        $basicAuth = base64_encode(config('services.ups.client_id') . ':' . config('services.ups.client_secret'));

    $res = Http::asForm()
        ->withHeaders([
            'Authorization' => 'Basic ' . $basicAuth,
        ])
        ->post(
            rtrim(config('services.ups.base_url'), '/') . '/security/v1/oauth/token',
            [
                'grant_type' => 'client_credentials',
            ]
        );

        if ($res->failed() || !$res->json('access_token')) {
            throw new \Exception('UPS Auth failed: ' . $res->body());
        }

        $this->token = $res->json('access_token');
    }

    public function validateAddress(array $address)
    {
        $res = Http::withToken($this->token)
            ->post(config('services.ups.base_url') . '/api/addressvalidation/v1/validate', [
                'address' => $address,
            ]);

        return $res->json();
    }

    public function createShipment(FgpOrder $order)
    {
    $items = $order->orderDetails()->with('flavor')->get();
    $totalCases = $items->sum('unit_number');

    // 3 cases per box
    $casesPerBox = 3;
    $boxCount = (int) ceil($totalCases / $casesPerBox);

    // 1. Allocate cases to boxes, tracking contents
    $cases = [];
    foreach ($items as $detail) {
        for ($i = 0; $i < $detail->unit_number; $i++) {
            $cases[] = [
                'detail_id' => $detail->id,
                'flavor' => $detail->flavor->name ?? 'Unknown',
                'sku' => $detail->flavor->sku ?? null,
            ];
        }
    }
    $boxes = array_chunk($cases, $casesPerBox);

    // 2. Build packages array for UPS
    $packages = [];
    foreach ($boxes as $boxIdx => $boxCases) {
        $packages[] = [
            "PackagingType" => ["Code" => "02"], // Customer supplied
            "Dimensions" => [
                "UnitOfMeasurement" => ["Code" => "IN"],
                "Length" => "23",
                "Width" => "20",
                "Height" => "17"
            ],
            "PackageWeight" => [
                "UnitOfMeasurement" => ["Code" => "LBS"],
                "Weight" => "49"
            ],
            // Optionally, include box contents for your own use (not sent to UPS)
            "BoxContents" => $boxCases
        ];
    }

    // 3. Build UPS payload (remove BoxContents before sending)
    $upsPackages = array_map(function($pkg) {
        $copy = $pkg;
        unset($copy['BoxContents']);
        return $copy;
    }, $packages);

    $payload = [
        "ShipmentRequest" => [
            "Shipment" => [
                "Shipper" => [
                    "Name" => "Frios HQ",
                    "ShipperNumber" => config('services.ups.account_number'),
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
                "Package" => $upsPackages
            ],
            "LabelSpecification" => [
                "LabelImageFormat" => ["Code" => "ZPL"]
            ]
        ]
    ];

    $res = \Http::withToken($this->token)
        ->post(config('services.ups.base_url') . '/api/shipments/v1/ship', $payload);

    $data = $res->json();

    if ($res->failed()) {
        throw new \Exception('UPS API failed: ' . $res->body());
    }

    // 4. Handle multiple labels/tracking numbers and save box contents
    $packageResults = data_get($data, 'ShipmentResponse.ShipmentResults.PackageResults');
    if (isset($packageResults['TrackingNumber'])) {
        $packageResults = [$packageResults];
    }

    foreach ($packageResults as $idx => $pkg) {
        $labelImage = base64_decode(data_get($pkg, 'ShippingLabel.GraphicImage'));
        $tracking = data_get($pkg, 'TrackingNumber');
        $shipmentId = data_get($data, 'ShipmentResponse.ShipmentResults.ShipmentIdentificationNumber');
        $file = "labels/ups_label_{$order->fgp_ordersID}_box" . ($idx + 1) . ".zpl";
        \Storage::disk('local')->put($file, $labelImage);

        // Save box contents as JSON for packing list
        UpsShipment::create([
            'fgp_ordersID' => $order->fgp_ordersID,
            'shipment_id' => $shipmentId,
            'tracking_number' => $tracking,
            'label_format' => 'ZPL',
            'label_file_path' => $file,
            'box_contents' => json_encode($boxes[$idx]), // <-- Save contents
            'raw_response' => $pkg,
        ]);
    }

        // Optionally return box contents with tracking numbers for packing list generation
        return collect($packageResults)->map(function($pkg, $idx) use ($boxes) {
            return [
                'tracking_number' => data_get($pkg, 'TrackingNumber'),
                'box_contents' => $boxes[$idx],
            ];
        });
    }
}

