<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FgpOrder;
use App\Models\Customer;
use App\Models\Franchise;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FgpOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        FgpOrder::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        $faker = Faker::create();
    
        // Get existing customers, franchises, and users for foreign key relationships
        $customers = Customer::pluck('id')->toArray();
        $franchises = Franchise::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
    
        // If no customers, franchises, or users exist, we'll create some basic ones
        if (empty($customers)) {
            $customers = [1, 2, 3];
        }
        if (empty($franchises)) {
            $franchises = [1, 2, 3];
        }
        if (empty($users)) {
            $users = [1, 2, 3];
        }
    
        $statuses = ['Pending', 'Paid', 'Shipped', 'Delivered'];
        $shipMethods = ['UPS Ground', 'UPS 2-Day Air', 'FedEx Ground', 'FedEx Express', 'USPS Priority Mail'];
        $shipstationStatuses = ['Awaiting Payment', 'Awaiting Shipment', 'Shipped', 'Delivered', 'Cancelled'];
    
        for ($i = 1; $i <= 10; $i++) {
            $status = $faker->randomElement($statuses);
            $isPaid = in_array($status, ['Paid', 'Shipped', 'Delivered']) ? 1 : 0;
            $isDelivered = $status === 'Delivered' ? 1 : 0;
            $deliveredAt = $isDelivered ? $faker->dateTimeBetween('-30 days', 'now') : null;
    
            FgpOrder::create([
                'franchise_id' => $faker->randomElement($franchises),
                'order_num' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'ACH_data' => json_encode([
                    'account_number' => $faker->bankAccountNumber,
                    'routing_number' => $faker->numerify('#########'),
                    'account_type' => $faker->randomElement(['checking', 'savings'])
                ]),
                'ship_to_name' => $faker->name,
                'ship_to_address1' => $faker->streetAddress,
                'ship_to_address2' => $faker->optional(0.3)->secondaryAddress,
                'ship_to_city' => $faker->city,
                'ship_to_state' => $faker->stateAbbr,
                'ship_to_zip' => $faker->postcode,
                'ship_to_country' => 'US',
                'ship_to_phone' => $faker->phoneNumber,
                'ship_method' => $faker->randomElement($shipMethods),
                'tracking_number' => in_array($status, ['Shipped', 'Delivered']) ? '1Z' . $faker->numerify('##########') : null,
                'shipstation_order_id' => $faker->numberBetween(100000, 999999),
                'shipstation_label_url' => in_array($status, ['Shipped', 'Delivered']) ? 'https://example.com/label/' . $faker->uuid : null,
                'shipstation_raw_response' => json_encode([
                    'orderId' => $faker->numberBetween(100000, 999999),
                    'orderKey' => $faker->uuid,
                    'orderDate' => $faker->dateTime->format('Y-m-d\TH:i:s.v\Z'),
                    'orderStatus' => $faker->randomElement($shipstationStatuses)
                ]),
                'amount' => $faker->randomFloat(2, 25.00, 500.00),
                'additional_charges' => $faker->randomFloat(2, 0.00, 100.00),
                'total_amount' => $faker->randomFloat(2, 25.00, 500.00),
                'is_paid' => $isPaid,
                'shipstation_status' => $faker->randomElement($shipstationStatuses),
                'delivered_at' => $deliveredAt,
                'shipstation_webhook_event' => $status === 'Delivered' ? 'item_shipped' : null,
               
                'created_by' => $faker->randomElement($users),
                'updated_by' => $faker->randomElement($users),
                'created_at' => $faker->dateTimeBetween('-90 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now')
            ]);
        }
    }
    
}
