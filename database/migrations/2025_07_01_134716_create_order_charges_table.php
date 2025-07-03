<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('fgp_order_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fgp_order_id')->constrained('fgp_orders', 'id');
            $table->string('charge_name');
            $table->decimal('charge_amount', 10, 2);
            $table->string('charge_type'); // used string() for more flexible
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fgp_order_charges');
    }
};
