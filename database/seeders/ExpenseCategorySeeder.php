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
        $now = Carbon::now();

        // Define the full category hierarchy
        $categories = [
            'Inventory & Supplies' => [
                'Frozen Gourmet Pops – Primary inventory costs',
                'Dry Ice / Ice Packs – Essential for keeping pops frozen',
                'Packaging & Napkins – Cups, spoons, napkins, serving trays',
                'Cleaning Supplies – Sanitizers, wipes, gloves',
                'Utensils & Equipment – Scoopers, knives, cutting boards',
            ],
            'Vehicle & Equipment Costs' => [
                'Truck Lease / Loan Payment – Monthly lease or purchase payment',
                'Truck Maintenance & Repairs – Oil changes, tire replacements, engine repairs',
                'Vehicle Insurance – Required for business operation',
                'Fuel Costs – Gas or diesel expenses',
                'Generator Fuel / Maintenance – If using a separate power source',
                'Permits & Registration – Food truck licensing, health permits, and local business registrations',
            ],
            'Staffing & Payroll' => [
                'Employee Wages – Hourly wages for staff',
                'Payroll Taxes – Social Security, Medicare, unemployment taxes',
                'Workers\' Compensation Insurance – Required in most states',
                'Uniforms & Branded Gear – Shirts, aprons, hats',
            ],
            'Commissary & Storage' => [
                'Commissary Rent – If required by the city for food truck operations',
                'Cold Storage Rental – Extra freezer space if needed',
                'Equipment Storage Fees – For off-truck storage of supplies',
            ],
            'Marketing & Advertising' => [
                'Social Media Ads – Paid promotions on Facebook, Instagram, etc.',
                'Branded Wraps & Stickers – Truck wraps, menu boards, promotional decals',
                'Website & SEO Costs – If maintaining a website',
                'Promotional Giveaways – Free samples, customer rewards',
                'Event Fees & Vendor Booth Costs – Entry fees for fairs, markets, and festivals',
            ],
            'Payment Processing & POS' => [
                'Credit Card Processing Fees – Stripe, Square, or PayPal transaction fees',
                'POS System Subscription – Monthly fee for POS software',
                'ACH & Bank Transfer Fees – If using invoicing for large orders',
            ],
            'Administrative & Office Expenses' => [
                'Business Licenses & Fees – Renewals, state filings',
                'Accounting & Bookkeeping – QuickBooks, Xero, or an accountant',
                'Software Subscriptions – Scheduling, payroll, or communication tools',
                'Phone & Internet – If using a mobile hotspot for POS',
            ],
            'Miscellaneous' => [
                'Event Commissions & Revenue Shares – If paying event organizers a percentage of sales',
                'Emergency Repairs – Unexpected equipment or truck breakdowns',
                'Donations & Sponsorships – Community engagement, school events',
            ],
        ];

        // Insert categories and subcategories
        foreach ($categories as $mainCategory => $subCategories) {
            $category = ExpenseCategory::create([
                'category' => $mainCategory,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($subCategories as $subCat) {
                // Split subcategory into name and description if possible
                $parts = explode(' – ', $subCat, 2);
                $name = trim($parts[0]);
                $desc = isset($parts[1]) ? trim($parts[1]) : null;

                ExpenseSubCategory::create([
                    'expense_category_id' => $category->id,
                    'category' => $name,
                    'description' => $desc,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        echo "✅ ExpenseCategorySeeder completed successfully!\n";
        echo "   - Created " . count($categories) . " Expense Categories\n";
        echo "   - Created " . array_sum(array_map('count', $categories)) . " Expense Sub-Categories\n";
    }
}
