<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FgpOrder;
use App\Models\FgpItem;
use App\Models\FgpCategory;
use App\Models\Franchise;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FgpOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get existing orders and items for foreign key relationships
        $orders = FgpOrder::pluck('id')->toArray();
        $items = FgpItem::pluck('id')->toArray();
        
        // If no orders or items exist, create some basic ones or exit
        if (empty($orders)) {
            $this->command->error('No FgpOrders found. Please run FgpOrderSeeder first.');
            return;
        }
        
        if (empty($items)) {
            $this->command->warn('No FgpItems found. Creating dummy items for testing.');
            
            // Get required foreign key values
            $firstUserId = \App\Models\User::first()?->id;
            $firstFranchiseId = \App\Models\Franchise::first()?->id;
            $firstCategoryId = \App\Models\FgpCategory::first()?->id;
            
            if (!$firstUserId) {
                $this->command->error('No users found. Please ensure users are created first.');
                return;
            }
            
            if (!$firstFranchiseId) {
                $this->command->error('No franchises found. Please run FranchiseSeeder first.');
                return;
            }
            
            if (!$firstCategoryId) {
                $this->command->error('No FGP categories found. Please run FgpCategorySeeder first.');
                return;
            }
            
            // Create some dummy FgpItems if none exist
            for ($i = 1; $i <= 5; $i++) {
                DB::table('fgp_items')->insert([
                    'franchise_id' => $firstFranchiseId,
                    'fgp_category_id' => $firstCategoryId,
                    'name' => 'Flavor Item ' . $i,
                    'description' => 'Delicious flavor item ' . $i,
                    'case_cost' => $faker->randomFloat(2, 10.00, 50.00),
                    'internal_inventory' => $faker->numberBetween(100, 1000),
                    'orderable' => true,
                    'split_factor' => $faker->numberBetween(12, 24),
                    'created_by' => $firstUserId,
                    'updated_by' => $firstUserId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $items = FgpItem::pluck('id')->toArray();
        }

        // Create 10 order items
        for ($i = 1; $i <= 10; $i++) {
            $orderId = $faker->randomElement($orders);
            $itemId = $faker->randomElement($items);
            $quantity = $faker->numberBetween(1, 20);
            $unitPrice = $faker->randomFloat(2, 5.00, 25.00);
            $totalPrice = $quantity * $unitPrice;
            
            DB::table('fgp_order_items')->insert([
                'fgp_order_id' => $orderId,
                'fgp_item_id' => $itemId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'price' => $totalPrice,
                'created_at' => $faker->dateTimeBetween('-60 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now')
            ]);
        }
        
        $this->command->info('Created 10 dummy FGP order items successfully!');
    }
} 