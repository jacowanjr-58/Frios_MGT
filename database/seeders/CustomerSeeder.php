<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Franchise;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing franchises to assign customers to
        $franchises = Franchise::pluck('id')->toArray();
        
        if (empty($franchises)) {
            $this->command->error('No franchises found. Please run FranchiseSeeder first.');
            return;
        }

        $customers = [
            [
                'franchise_id' => $franchises[array_rand($franchises)],
                'name' => 'Sarah Johnson',
                'address1' => '456 Oak Street',
                'address2' => 'Apt 12B',
                'zip_code' => '78701',
                'state' => 'TX',
                'email' => 'sarah.johnson@email.com',
                'phone' => '(512) 555-1234',
                'notes' => 'Regular customer, prefers vanilla flavors. Birthday parties frequent.',
            ],
            [
                'franchise_id' => $franchises[array_rand($franchises)],
                'name' => 'Michael Rodriguez',
                'address1' => '789 Elm Avenue',
                'address2' => null,
                'zip_code' => '75240',
                'state' => 'TX',
                'email' => 'michael.rodriguez@gmail.com',
                'phone' => '(214) 555-5678',
                'notes' => 'Corporate event coordinator. Books large orders for company events.',
            ],
            [
                'franchise_id' => $franchises[array_rand($franchises)],
                'name' => 'Emily Chen',
                'address1' => '321 Pine Boulevard',
                'address2' => 'Suite 5',
                'zip_code' => '77008',
                'state' => 'TX',
                'email' => 'emily.chen@outlook.com',
                'phone' => '(713) 555-9012',
                'notes' => 'School teacher. Orders for classroom celebrations and end-of-year parties.',
            ],
            [
                'franchise_id' => $franchises[array_rand($franchises)],
                'name' => 'David Thompson',
                'address1' => '654 Maple Drive',
                'address2' => null,
                'zip_code' => '78205',
                'state' => 'TX',
                'email' => 'david.thompson@yahoo.com',
                'phone' => '(210) 555-3456',
                'notes' => 'Wedding planner. Needs custom flavors and large quantities for events.',
            ],
            [
                'franchise_id' => $franchises[array_rand($franchises)],
                'name' => 'Jennifer Martinez',
                'address1' => '987 Cedar Lane',
                'address2' => 'Building C, Unit 8',
                'zip_code' => '76164',
                'state' => 'TX',
                'email' => 'jennifer.martinez@email.com',
                'phone' => '(817) 555-7890',
                'notes' => 'PTA mom. Organizes school fundraisers and sports team celebrations.',
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = Customer::create($customerData);
            $franchise = Franchise::find($customerData['franchise_id']);
            $this->command->info("Created customer '{$customer->name}' for franchise '{$franchise->business_name}'");
        }

        $this->command->info('Created 5 customer records successfully!');
    }
} 