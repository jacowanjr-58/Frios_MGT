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
        if (! Schema::hasTable('payments')) {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users','id');
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->foreignId('fgp_order_id')->constrained('fgp_orders','id');
            $table->json('items'); // List of items paid for
            $table->unsignedInteger('quantity');
            $table->decimal('total', 10, 2);
            $table->decimal('taxes', 10, 2)->nullable();
            $table->timestamps();
        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
