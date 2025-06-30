<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpCategoryFgpItemTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fgp_category_fgp_item')->insert([
            ['fgp_category_id' => 2,  'fgp_item_id' => 2],
            // ['fgp_category_id' => 2,  'fgp_item_id' => 5],
            // ... continue for every pivot row from fgp_category_fgp_item.sql ...
        ]);
    }
}
