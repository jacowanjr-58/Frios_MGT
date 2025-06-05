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
        if (! Schema::hasTable('stripes')) {
        Schema::create('stripes', function (Blueprint $table) {
            $table->id();
            $table->string('franchisee_id');
            $table->text('public_key');
            $table->text('secret_key');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripes');
    }
};
