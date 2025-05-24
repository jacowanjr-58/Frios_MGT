<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShipStationService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.shipstation.key');
        $this->apiSecret = config('services.shipstation.secret');
        $this->baseUrl = 'https://ssapi.shipstation.com';
    }

    public function sendOrder(array $payload, bool $isPaid = false, string $invoiceId = null)
    {
        $payload = [
            'orderNumber' => $payload['order_number'],
            'orderDate' => now()->toIso8601String(),
            'customerEmail' => $payload['order_email'],
            'shipTo' => [
                'name' => $payload['ship_to_name'],
                'street1' => $payload['ship_to_address1'],
                'street2' => $payload['ship_to_address2'],
                'city' => $payload['ship_to_city'],
                'state' => $payload['ship_to_state'],
                'postalCode' => $payload['ship_to_zip'],
                'country' => $payload['ship_to_country'] ?? 'US',
                'phone' => $payload['ship_to_phone'],
            ],
            'billTo' => [
                'name' => $payload['order_email'],
            ],
            'orderStatus' => $payload['is_paid'] ? 'awaiting_shipment' : 'awaiting_payment',
            'amountPaid' => $payload['is_paid'] ? $payload['grandTotal'] : 0,
            'paymentMethod' => $payload['is_paid'] ? 'credit_card' : 'invoice',
            'customFields' => [
                ['name' => 'Invoice ID', 'value' => $payload['invoice_id'] ?? ''],
                ['name' => 'Payment Status', 'value' => $payload['is_paid'] ? 'Paid' : 'Pending'],
            ],
            'items' => $payload['orderItems'] ?? [], // ✅ Add items array here
        ];

        try {
            $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}/orders/createorder", $payload);

            if ($response->failed()) {
                Log::error('ShipStation Order Creation Failed', ['response' => $response->body()]);
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('ShipStation API Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}


//shipstation order template

/* POST /orders/createorder HTTP/1.1
Host: ssapi.shipstation.com
Authorization: __YOUR_AUTH_HERE__
Content-Type: application/json

{
  "orderNumber": "TEST-ORDER-API-DOCS",
  "orderKey": "0f6bec18-3e89-4881-83aa-f392d84f4c74",
  "orderDate": "2015-06-29T08:46:27.0000000",
  "paymentDate": "2015-06-29T08:46:27.0000000",
  "shipByDate": "2015-07-05T00:00:00.0000000",
  "orderStatus": "awaiting_shipment",
  "customerId": 37701499,
  "customerUsername": "headhoncho@whitehouse.gov",
  "customerEmail": "headhoncho@whitehouse.gov",
  "billTo": {
    "name": "The President",
    "company": null,
    "street1": null,
    "street2": null,
    "street3": null,
    "city": null,
    "state": null,
    "postalCode": null,
    "country": null,
    "phone": null,
    "residential": null
  },
  "shipTo": {
    "name": "The President",
    "company": "US Govt",
    "street1": "1600 Pennsylvania Ave",
    "street2": "Oval Office",
    "street3": null,
    "city": "Washington",
    "state": "DC",
    "postalCode": "20500",
    "country": "US",
    "phone": "555-555-5555",
    "residential": true
  },
  "items": [
    {
      "lineItemKey": "vd08-MSLbtx",
      "sku": "ABC123",
      "name": "Test item #1",
      "imageUrl": null,
      "weight": {
        "value": 24,
        "units": "ounces"
      },
      "quantity": 2,
      "unitPrice": 99.99,
      "taxAmount": 2.5,
      "shippingAmount": 5,
      "warehouseLocation": "Aisle 1, Bin 7",
      "options": [
        {
          "name": "Size",
          "value": "Large"
        }
      ],
      "productId": 123456,
      "fulfillmentSku": null,
      "adjustment": false,
      "upc": "32-65-98"
    },
    {
      "lineItemKey": null,
      "sku": "DISCOUNT CODE",
      "name": "10% OFF",
      "imageUrl": null,
      "weight": {
        "value": 0,
        "units": "ounces"
      },
      "quantity": 1,
      "unitPrice": -20.55,
      "taxAmount": null,
      "shippingAmount": null,
      "warehouseLocation": null,
      "options": [],
      "productId": 123456,
      "fulfillmentSku": "SKU-Discount",
      "adjustment": true,
      "upc": null
    }
  ],
  "amountPaid": 218.73,
  "taxAmount": 5,
  "shippingAmount": 10,
  "customerNotes": "Please ship as soon as possible!",
  "internalNotes": "Customer called and would like to upgrade shipping",
  "gift": true,
  "giftMessage": "Thank you!",
  "paymentMethod": "Credit Card",
  "requestedShippingService": "Priority Mail",
  "carrierCode": "fedex",
  "serviceCode": "fedex_2day",
  "packageCode": "package",
  "confirmation": "delivery",
  "shipDate": "2015-07-02",
  "weight": {
    "value": 25,
    "units": "ounces"
  },
  "dimensions": {
    "units": "inches",
    "length": 7,
    "width": 5,
    "height": 6
  },
  "insuranceOptions": {
    "provider": "carrier",
    "insureShipment": true,
    "insuredValue": 200
  },
  "internationalOptions": {
    "contents": null,
    "customsItems": null
  },
  "advancedOptions": {
    "warehouseId": null,
    "nonMachinable": false,
    "saturdayDelivery": false,
    "containsAlcohol": false,
    "mergedOrSplit": false,
    "mergedIds": [],
    "parentId": null,
    "storeId": null,
    "customField1": "Custom data that you can add to an order. See Custom Field #2 & #3 for more info!",
    "customField2": "Per UI settings, this information can appear on some carrier's shipping labels. See link below",
    "customField3": "https://help.shipstation.com/hc/en-us/articles/206639957",
    "source": "Webstore",
    "billToParty": null,
    "billToAccount": null,
    "billToPostalCode": null,
    "billToCountryCode": null
  },
  "tagIds": [
    53974
  ]
} */