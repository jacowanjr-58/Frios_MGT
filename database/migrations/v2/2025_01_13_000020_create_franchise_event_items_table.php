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
        if (! Schema::hasTable('franchise_event_items')) {
        Schema::create('franchise_event_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events','id');
            $table->unsignedInteger('in_stock')->nullable();
            $table->unsignedInteger('orderable');
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchise_event_items');
    }
};
