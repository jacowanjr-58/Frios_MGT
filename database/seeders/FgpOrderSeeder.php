<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FgpOrder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Franchise;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FgpOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users, customers, and franchises for foreign key relationships
        $users = User::pluck('id')->toArray();
        $customers = Customer::pluck('id')->toArray();
        $franchises = Franchise::pluck('id')->toArray();

        // If no data exists, create some basic records first
        if (empty($users)) {
            $this->command->error('No users found. Please run user seeders first.');
            return;
        }
        if (empty($franchises)) {
            $this->command->error('No franchises found. Please run FranchiseSeeder first.');
            return;
        }

        // Sample shipping addresses - Updated to Texas cities to match franchise locations
        $shippingAddresses = [
            [
                'name' => 'John Smith',
                'address1' => '123 Main Street',
                'address2' => 'Apt 4B',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '512-123-4567'
            ],
            [
                'name' => 'Sarah Johnson',
                'address1' => '456 Oak Avenue',
                'address2' => '',
                'city' => 'Dallas',
                'state' => 'TX',
                'zip' => '75240',
                'phone' => '214-987-6543'
            ],
            [
                'name' => 'Mike Wilson',
                'address1' => '789 Pine Street',
                'address2' => 'Suite 200',
                'city' => 'Houston',
                'state' => 'TX',
                'zip' => '77008',
                'phone' => '713-456-7890'
            ],
            [
                'name' => 'Emily Davis',
                'address1' => '321 Elm Drive',
                'address2' => '',
                'city' => 'San Antonio',
                'state' => 'TX',
                'zip' => '78205',
                'phone' => '210-234-5678'
            ],
            [
                'name' => 'Robert Brown',
                'address1' => '654 Maple Lane',
                'address2' => 'Unit 12',
                'city' => 'Fort Worth',
                'state' => 'TX',
                'zip' => '76164',
                'phone' => '817-345-6789'
            ],
            [
                'name' => 'Lisa Garcia',
                'address1' => '987 Cedar Court',
                'address2' => '',
                'city' => 'El Paso',
                'state' => 'TX',
                'zip' => '79912',
                'phone' => '915-567-8901'
            ]
        ];

        $statuses = ['Pending', 'Paid', 'Shipped', 'Delivered'];
        $shipMethods = ['Standard', 'Express', 'Overnight', 'Ground'];
        $shipstationStatuses = ['awaiting_payment', 'awaiting_shipment', 'shipped', 'on_hold', 'cancelled', 'pending_fulfillment'];

        // Create 30 dummy orders
        for ($i = 1; $i <= 30; $i++) {
            $address = $shippingAddresses[array_rand($shippingAddresses)];
            $status = $statuses[array_rand($statuses)];
            $isShipped = in_array($status, ['Shipped', 'Delivered']);
            $isPaid = in_array($status, ['Paid', 'Shipped', 'Delivered']);
            $isDelivered = $status === 'Delivered';
            
            // Generate a date between 3 months ago and now
            $transactionDate = Carbon::now()->subDays(rand(1, 90));
            
            // Map shipstation status based on order status
            $shipstationStatus = match($status) {
                'Pending' => $isPaid ? 'awaiting_shipment' : 'awaiting_payment',
                'Paid' => 'awaiting_shipment',
                'Shipped' => 'shipped',
                'Delivered' => 'shipped',
                default => 'awaiting_payment'
            };

            $orderData = [
                'user_id' => $users[array_rand($users)],
                'franchise_id' => $franchises[array_rand($franchises)],
                'customer_id' => !empty($customers) ? (rand(0, 1) ? $customers[array_rand($customers)] : null) : null,
                'date_transaction' => $transactionDate,
                'status' => $status,
                'is_paid' => $isPaid,
                'is_delivered' => $isDelivered,
                'ship_to_name' => $address['name'],
                'ship_to_address1' => $address['address1'],
                'ship_to_address2' => $address['address2'],
                'ship_to_city' => $address['city'],
                'ship_to_state' => $address['state'],
                'ship_to_zip' => $address['zip'],
                'ship_to_country' => 'US',
                'ship_to_phone' => $address['phone'],
                'ship_method' => $shipMethods[array_rand($shipMethods)],
                'tracking_number' => $isShipped ? '1Z' . strtoupper(substr(md5($i), 0, 16)) : null,
                'shipstation_status' => $shipstationStatus,
                'label_created_at' => $isShipped ? $transactionDate->copy()->addDays(1) : null,
                'delivered_at' => $isDelivered ? $transactionDate->copy()->addDays(rand(3, 7)) : null,
                'created_by' => $users[array_rand($users)],
                'updated_by' => $users[array_rand($users)],
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate->copy()->addDays(rand(0, 3))
            ];

            FgpOrder::create($orderData);
        }

        $this->command->info('Created 30 dummy FGP orders successfully!');
    }
}
