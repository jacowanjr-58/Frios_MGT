<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fgp_order_discrepancies')) {
            Schema::create('fgp_order_discrepancies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fgp_order_id')->constrained('fgp_orders', 'id');
                $table->foreignId('fgp_order_item_id')->constrained('fgp_order_items', 'id');
                $table->foreignId('user_id')->constrained('users', 'id');
                $table->integer('quantity_ordered')->nullable();
                $table->integer('quantity_received')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fgp_order_discrepancies');
    }
};
