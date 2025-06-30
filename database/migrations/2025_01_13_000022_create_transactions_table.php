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
        if (! Schema::hasTable('transactions')) {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->foreignId('customer_id')->nullable()->constrained('customers','id');
            $table->foreignId('user_id')->nullable()->constrained('users','id');
            $table->string('transactionable_type');
            $table->string('transactionable_id');
            $table->string('cardholder_name');
            $table->double('amount');
            $table->string('currency')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_payment_method')->nullable();
            $table->string('stripe_client_secret')->nullable();
            $table->string('stripe_status')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
