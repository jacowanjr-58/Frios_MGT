<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Carbon\Carbon;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 Expense Categories
        $categories = [
            [
                'category' => 'Office Expenses',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'Marketing & Advertising',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'Equipment & Supplies',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'Utilities & Maintenance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'Travel & Transportation',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert categories and get their IDs
        foreach ($categories as $categoryData) {
            ExpenseCategory::create($categoryData);
        }

        // Get the created categories
        $officeExpenses = ExpenseCategory::where('category', 'Office Expenses')->first();
        $marketing = ExpenseCategory::where('category', 'Marketing & Advertising')->first();
        $equipment = ExpenseCategory::where('category', 'Equipment & Supplies')->first();
        $utilities = ExpenseCategory::where('category', 'Utilities & Maintenance')->first();
        $travel = ExpenseCategory::where('category', 'Travel & Transportation')->first();

        // Create 5 Expense Sub Categories (1 for each main category)
        $subCategories = [
            [
                'expense_category_id' => $officeExpenses->id,
                'category' => 'Office Supplies',
                'description' => 'Stationery, paper, pens, and other office supplies required for daily operations.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $marketing->id,
                'category' => 'Social Media Advertising',
                'description' => 'Expenses related to social media campaigns, sponsored posts, and digital marketing.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $equipment->id,
                'category' => 'Kitchen Equipment',
                'description' => 'Equipment purchases and maintenance for kitchen operations including freezers, blenders, and tools.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $utilities->id,
                'category' => 'Electricity Bills',
                'description' => 'Monthly electricity bills and power-related expenses for the franchise location.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $travel->id,
                'category' => 'Fuel & Gas',
                'description' => 'Vehicle fuel costs and transportation expenses for business operations.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Create additional sub-categories to have more variety
        $additionalSubCategories = [
            [
                'expense_category_id' => $officeExpenses->id,
                'category' => 'Software Subscriptions',
                'description' => 'Monthly software subscriptions and digital tools for business operations.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $marketing->id,
                'category' => 'Print Materials',
                'description' => 'Brochures, flyers, business cards, and other printed marketing materials.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $equipment->id,
                'category' => 'POS System',
                'description' => 'Point of sale system equipment, tablets, printers, and related hardware.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $utilities->id,
                'category' => 'Internet & Phone',
                'description' => 'Internet service, phone bills, and communication expenses.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'expense_category_id' => $travel->id,
                'category' => 'Delivery Expenses',
                'description' => 'Delivery vehicle costs, maintenance, and transportation for customer orders.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert all sub-categories
        foreach (array_merge($subCategories, $additionalSubCategories) as $subCategoryData) {
            ExpenseSubCategory::create($subCategoryData);
        }

        // Output completion message
        echo "✅ ExpenseCategorySeeder completed successfully!\n";
        echo "   - Created 5 Expense Categories\n";
        echo "   - Created 10 Expense Sub-Categories (2 per category)\n";
    }
} 