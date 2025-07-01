<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpItemsTableSeeder extends Seeder
{
    public function run()
    {
        // Make sure you’ve copied fgp_items.sql into database/seeders/sql/
        $path = database_path('seeders/sql/fgp_items.sql');
        DB::unprepared(file_get_contents($path));
    }
}
