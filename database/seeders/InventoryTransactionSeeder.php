<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryTransaction;

class InventoryTransactionSeeder extends Seeder
{
    public function run()
    {
        InventoryTransaction::create([
            'inventory_id' => 3,
            'type'         => 'add',
            'quantity'     => 20,
            'reference'    => 'Seeder',
            'notes'        => 'Initial custom item seed',
            'created_by'   => 42
        ]);

        InventoryTransaction::create([
            'inventory_id' => 2,
            'type'         => 'add',
            'quantity'     => 50,
            'reference'    => 'Seeder',
            'notes'        => 'Initial flavor seed',
            'created_by'   => 42
        ]);
    }
}
