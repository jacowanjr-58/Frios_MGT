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
                $table->id('franchise_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('business_name');
                $table->string('address1');
                $table->string('address2')->nullable();
                $table->string('city');
                $table->string('state');
                $table->string('zip_code');
                $table->string('location_zip')->nullable();
                $table->string('ACH_data_API')->nullable();
                $table->string('pos_service_API')->nullable();
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('user_id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks to allow dropping tables with dependencies
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::dropIfExists('franchises');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::dropIfExists('franchises');
    }
};
