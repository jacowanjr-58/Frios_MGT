<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FgpCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fgp_categories')->insert([
            ['id' => 1,  'name' => 'Availability',                  'parent_id' => null, 'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 2,  'name' => 'Signature',                     'parent_id' => 1,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 3,  'name' => 'Seasonal',                      'parent_id' => 1,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 4,  'name' => 'Flavor',                        'parent_id' => null, 'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 5,  'name' => 'Creamy',                        'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 6,  'name' => 'Fruity',                        'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 7,  'name' => 'No Sugar Added',                'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 8,  'name' => 'Gluten Free',                   'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 9,  'name' => 'Dye Free',                      'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 10, 'name' => 'Vegan',                         'parent_id' => 4,    'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 11, 'name' => 'Allergen',                      'parent_id' => null, 'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 12, 'name' => 'Nut Free',                      'parent_id' => 11,   'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 13, 'name' => 'Wheat Free',                    'parent_id' => 11,   'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 14, 'name' => 'Soy Free',                      'parent_id' => 11,   'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 15, 'name' => 'Dairy Free',                    'parent_id' => 11,   'created_at' => '2025-06-28 23:37:11', 'updated_at' => '2025-06-28 23:37:11'],
            ['id' => 16, 'name' => 'Protein Plus',                  'parent_id' => 4,    'created_at' => '2025-06-28 23:40:30', 'updated_at' => '2025-06-28 23:48:27'],
        ]);
    }
}
