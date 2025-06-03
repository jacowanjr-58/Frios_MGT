<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMasterTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_master', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->unsignedBigInteger('franchisee_id');
            $table->unsignedBigInteger('fgp_item_id')->nullable();
            $table->string('custom_item_name')->nullable();
            $table->integer('total_quantity')->default(0);
            $table->timestamps();

            $table->foreign('franchisee_id')->references('franchisee_id')->on('franchisees')->onDelete('cascade');
            $table->foreign('fgp_item_id')->references('fgp_item_id')->on('fgp_items')->onDelete('set null');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_master');
    }
}
