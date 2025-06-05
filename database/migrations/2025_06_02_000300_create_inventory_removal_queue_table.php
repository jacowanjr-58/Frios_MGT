<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        if (! Schema::hasTable('inventory_removal_queue')) {
        Schema::create('inventory_removal_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('quantity');
            $table->string('sale_reference')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->timestamps();

            $table->foreign('inventory_id')->references('inventory_id')->on('inventory_master')->onDelete('cascade');
            $table->foreign('location_id')->references('locations_ID')->on('locations')->onDelete('cascade');
            $table->foreign('requested_by')->references('user_id')->on('users')->onDelete('cascade');
        });
        }
    }

    public function down():void
    {
        Schema::dropIfExists('inventory_removal_queue');
    }
};
