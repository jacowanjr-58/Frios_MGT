<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('location_id');
            $table->integer('allocated_quantity');
            $table->timestamps();

            $table->foreign('inventory_id')->references('inventory_id')->on('inventory_master')->onDelete('cascade');
            $table->foreign('location_id')->references('locations_ID')->on('locations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_allocations');
    }
}
