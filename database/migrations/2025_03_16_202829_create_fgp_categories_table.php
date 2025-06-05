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
        if (! Schema::hasTable('fgp_categories')) {
        Schema::create('fgp_categories', function (Blueprint $table) {
            $table->id('category_ID');
            $table->string('name');
            $table->json('type'); // Change from string to json for multiple values
            $table->timestamps();
        });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_categories');
    }
};
