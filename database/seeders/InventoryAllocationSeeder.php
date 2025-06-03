<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryAllocation;

class InventoryAllocationSeeder extends Seeder
{
    public function run()
    {
        // Assuming location_id 1 exists
        InventoryAllocation::create([
            'inventory_id'       => 2,
            'location_id'        => 1,
            'allocated_quantity' => 10,
        ]);
    }
}
