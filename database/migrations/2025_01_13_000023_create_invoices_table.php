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
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('franchises','id');
                $table->foreignId('customer_id')->constrained('customers','id');
                $table->string('direction')->default('receivable'); //'payable', 'receivable'
                $table->string('name');
                $table->date('date');
                $table->decimal('total_price', 10, 2);
                $table->decimal('tax_price', 10, 2);
                $table->date('due_date')->nullable();
                $table->string('payment_status')->default('unpaid'); //'unpaid', 'partial', 'paid'
                $table->text('notes_internal')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
