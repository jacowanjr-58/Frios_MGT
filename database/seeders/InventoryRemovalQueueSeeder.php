<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryRemovalQueue;

class InventoryRemovalQueueSeeder extends Seeder
{
    public function run()
    {
        InventoryRemovalQueue::create([
            'inventory_id'   => 2,
            'location_id'    => 1,
            'quantity'       => 5,
            'sale_reference' => 'Test Sale',
            'status'         => 'pending',
            'requested_by'   => 42,
        ]);
    }
}
