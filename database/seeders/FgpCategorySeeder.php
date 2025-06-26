<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FgpCategory;

class FgpCategorySeeder extends Seeder
{
    public function run(): void
    {

       
        $categories = [
            [
                'type' => 'availability',
                'values' => ['signature', 'seasonal']
            ],
            [
                'type' => 'type',
                'values' => ['creamy', 'fruity', 'no sugar added', 'gluten free', 'dye free', 'vegan']
            ],
            [
                'type' => 'allergen',
                'values' => ['nut free', 'wheat free', 'soy free', 'dairy free']
            ]
        ];

        foreach ($categories as $data) { 
            foreach ($data['values'] as $value) {
            FgpCategory::create([
                'type' => ($data['type']), // Optional: for readability
                'name' => $value,
            ]);
            }
            $this->command->info("Created category: {$data['type']}");
        }

        $this->command->info('✅ FGP categories created based on UI grouping.');
    }
}
