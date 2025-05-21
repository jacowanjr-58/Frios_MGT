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
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('franchisee_id');
            $table->integer('fgp_order_id');
            $table->string('cardholder_name');
            $table->double('amount');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_payment_method')->nullable();
            $table->string('stripe_currency', 10)->nullable();
            $table->string('stripe_client_secret')->nullable();
            $table->string('stripe_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_transactions');
    }
};
