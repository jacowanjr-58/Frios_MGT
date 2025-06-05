<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('additionalcharges')) {
        Schema::create('additionalcharges', function (Blueprint $table) {
            $table->id('additionalcharges_id');
            $table->string('charge_name');
            $table->decimal('charge_price', 10, 2);
            $table->enum('charge_optional', ['optional', 'required'])->default('optional');
            $table->enum('charge_type', ['fixed', 'percentage'])->default('fixed');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('additionalcharges');
    }
};
