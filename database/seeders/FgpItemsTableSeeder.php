<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpItemsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fgp_items')->insert([
            // Insert each row from fgp_items.sql here, e.g.:
            [
                'id'                 => 2,
                'franchise_id'       => 3,
                'name'               => 'Birthday Cake',
                'description'        => 'Classic birthday cake ice cream in a pop form',
                'case_cost'          => 65.00,
                'internal_inventory' => 10,
                'split_factor'       => 48,
                'dates_available'    => json_encode(["1","2","3","4","5","6","7","8","9","10","11","12"]),
                'image1'             => 'images/fgp_items/ee5UXMqlrUuUiUhHhcBV6Lyx9fYsLX1yTcfm57UV.png',
                'image2'             => null,
                'image3'             => null,
                'orderable'          => 1,
                'created_by'         => 2,
                'updated_by'         => 2,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // ... repeat for the remaining items ...
        ]);
    }
}
