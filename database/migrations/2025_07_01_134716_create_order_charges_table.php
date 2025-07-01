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
            $table->unsignedBigInteger('order_id');
            $table->string('charges_name');
            $table->decimal('charge_amount', 10, 2);
            $table->enum('charge_type', ['fixed', 'percentage']); // or use string() if it's more flexible
            $table->timestamps();

            // Optional: Add foreign key if `orders` table exists
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fgp_order_charges');
    }
};
