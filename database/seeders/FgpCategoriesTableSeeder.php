<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['id' => 1, 'name' => 'Availability', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Signature', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Seasonal', 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Flavor', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Creamy', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Fruity', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'No Sugar Added', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Gluten Free', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Dye Free', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Vegan', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Allergen', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => 'Nut Free', 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => 'Wheat Free', 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => 'Soy Free', 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => 'Dairy Free', 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'name' => 'Protein Plus', 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('fgp_categories')->insert($categories);
    }
}
