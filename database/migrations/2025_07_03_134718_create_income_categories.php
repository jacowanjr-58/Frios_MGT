<?php
// create_income_categories.php PLAID Integration

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('income_categories')) {
            Schema::create('income_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->timestamps();
});
        }

        // Insert default income categories
        $defaultCategories = [
            'Sales Income',
            'Wholesale Income',
            'Service Income',
            'Tips Income',
            'Other Income',
        ];

        foreach ($defaultCategories as $category) {
            DB::table('income_categories')->insert([
                'category' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_categories');
    }
};
