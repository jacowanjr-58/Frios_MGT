<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('expenses')) {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->foreignId('expense_category_id')->constrained('expense_categories','id');
            $table->foreignId('expense_sub_category_id')->constrained('expense_sub_categories','id');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->foreignId('created_by')->constrained('users','id');
            $table->foreignId('updated_by')->constrained('users','id');
            $table->timestamps();

        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
