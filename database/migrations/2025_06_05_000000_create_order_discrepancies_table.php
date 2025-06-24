<?php

// database/migrations/2025_06_05_000000_create_order_discrepancies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_discrepancies')) {
            // Create the order_discrepancies table

            Schema::create('order_discrepancies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('order_detail_id');
                $table->integer('quantity_ordered');
                $table->integer('quantity_received');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('order_id')
                    ->references('id')
                    ->on('fgp_orders')
                    ->onDelete('cascade');

                $table->foreign('order_detail_id')
                    ->references('id')
                    ->on('fgp_order_details')
                    ->onDelete('cascade');
            });
        }

    }


    public function down():void
    {
        Schema::dropIfExists('order_discrepancies');
    }
};
