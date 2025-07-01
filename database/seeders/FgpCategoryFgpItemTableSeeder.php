<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpCategoryFgpItemTableSeeder extends Seeder
{
    public function run()
    {
        // Make sure you’ve copied fgp_category_fgp_item.sql into database/seeders/sql/
        $path = database_path('seeders/sql/fgp_category_fgp_item.sql');
        DB::unprepared(file_get_contents($path));
    }
}
