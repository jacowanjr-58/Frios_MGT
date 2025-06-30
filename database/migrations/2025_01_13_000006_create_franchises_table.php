<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('franchises')) {
            Schema::create('franchises', function (Blueprint $table) {
                $table->id();
                $table->string('business_name');
                $table->string('contact_number')->nullable();
                $table->string('address1');
                $table->string('address2')->nullable();
                $table->string('city');
                $table->string('state');
                $table->string('zip_code');
                $table->string('location_zip')->nullable();
                $table->string('ACH_data_API')->nullable();
                $table->string('pos_service_API')->nullable();
                $table->string('frios_territory_name')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users','id');
                $table->foreignId('updated_by')->nullable()->constrained('users','id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     
        Schema::dropIfExists('franchises');
    }
};
