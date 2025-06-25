<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('additional_charges')) {
        Schema::create('additional_charges', function (Blueprint $table) {
            $table->id();   
            $table->foreignId('franchise_id')->constrained('franchises','id');
            $table->string('charge_name');
            $table->decimal('charge_price', 10, 2);
            $table->string('charge_optional')->default('optional');
            $table->string('charge_type')->default('fixed');
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->constrained('users','id');
            $table->foreignId('updated_by')->constrained('users','id');
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('additional_charges');
    }
};
