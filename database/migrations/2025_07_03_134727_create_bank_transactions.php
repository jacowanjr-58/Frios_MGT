<?php
// create_bank_transactions_table.php PLAID Integration

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
        if (! Schema::hasTable('bank_transactions')) {
            Schema::create('bank_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('franchise_id');
                $table->unsignedBigInteger('bank_account_id');
                $table->string('transaction_id')->unique();
                $table->date('date');
                $table->string('name');
                $table->decimal('amount', 12, 2);
                $table->string('category')->nullable(); // PLAID delivered category
                $table->string('sub_category')->nullable(); // PLAID delivered sub-category
                // Add category links
                $table->unsignedBigInteger('income_category_id')->nullable();
                $table->unsignedBigInteger('expense_category_id')->nullable();
                $table->unsignedBigInteger('expense_sub_category_id')->nullable();
                $table->timestamps();
            });

            // Add foreign key constraints
            Schema::table('bank_transactions', function (Blueprint $table) {
                $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade');
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade');
                $table->foreign('income_category_id')->references('id')->on('income_categories')->nullOnDelete();
                $table->foreign('expense_category_id')->references('id')->on('expense_categories')->nullOnDelete();
                $table->foreign('expense_sub_category_id')->references('id')->on('expense_sub_categories')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
