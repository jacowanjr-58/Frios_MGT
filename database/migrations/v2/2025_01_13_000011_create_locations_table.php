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
        if (! Schema::hasTable('locations')) {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId( 'franchise_id')->constrained('franchises', 'id');
            $table->string('name');
            $table->string('type',)->nullable();
            $table->timestamps();
        
        });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
        Schema::dropIfExists('locations');
        
    }
};
