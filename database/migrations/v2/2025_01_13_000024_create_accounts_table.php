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
        if (! Schema::hasTable('accounts')) {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->string('cardholder_name')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_payment_method_id')->nullable();
            $table->string('stripe_token')->nullable();
            $table->unsignedInteger('is_active')->default(0);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
