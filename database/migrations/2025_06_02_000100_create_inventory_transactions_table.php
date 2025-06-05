<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        if (! Schema::hasTable('inventory_transactions')) {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('inventory_id');
            $table->enum('type', ['add', 'remove', 'transfer', 'adjust']);
            $table->integer('quantity');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

             $table->foreign('inventory_id')
          ->references('inventory_id')    // ← parent column
          ->on('inventory_master')        // ← parent table exactly
          ->cascadeOnDelete();
               $table->foreign('created_by')
          ->references('user_id')     // <-- note “user_id” here
          ->on('users')
          ->cascadeOnDelete();
        });
        }
    }

    public function down():void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
