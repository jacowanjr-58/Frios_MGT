<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('franchisees')) {
        Schema::create('franchisees', function (Blueprint $table) {
            $table->id('franchisee_id');
            $table->string('business_name');
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 5)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('location_zip')->nullable();
            $table->json('ACH_data_API')->nullable();
            $table->json('pos_service_API')->nullable();
            $table->timestamps();
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchisees');
    }
};
