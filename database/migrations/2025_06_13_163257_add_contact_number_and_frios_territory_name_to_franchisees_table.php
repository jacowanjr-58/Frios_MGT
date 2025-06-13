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
        Schema::table('franchisees', function (Blueprint $table) {
            $table->string('contact_number')->after('business_name');
            $table->string('frios_territory_name')->nullable()->after('contact_number');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franchisees', function (Blueprint $table) {
         
            $table->dropColumn(['contact_number', 'frios_territory_name']);
        });
    }
};
