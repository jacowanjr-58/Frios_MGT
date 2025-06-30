<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryMaster;

class InventoryMasterSeeder extends Seeder
{
    public function run()
    {
        // Sample custom item
        InventoryMaster::create([
            'franchisee_id'    => 11,
            'fgp_item_id'      => null,
            'custom_item_name' => 'Sample Custom Item',
            'total_quantity'   => 20,
        ]);

        // Sample real flavor (assuming fgp_item_id 1 exists)
        InventoryMaster::create([
            'franchisee_id'    => 11,
            'fgp_item_id'      => 14,
            'custom_item_name' => null,
            'total_quantity'   => 50,
        ]);
    }
}
