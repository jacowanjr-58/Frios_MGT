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
        if (! Schema::hasTable('events')) {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->foreignId('customer_id')->nullable()->constrained('customers','id');
            $table->string('name');
            $table->string('type')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('staff_assigned')->nullable();
            $table->double('expected_sales')->nullable();
            $table->double('actual_sales')->nullable();
            $table->double('costs')->nullable();
            $table->text('notes')->nullable();
            $table->json('resources_selection')->nullable();
            $table->string('status')->default('scheduled'); //'scheduled', 'tentative', 'staffed'
            $table->string('planned_payment')->nullable(); //'cash', 'check', 'invoice', 'credit-card'
            $table->timestamps();


        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
