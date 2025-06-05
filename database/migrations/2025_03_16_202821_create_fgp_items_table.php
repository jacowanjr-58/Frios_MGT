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
        if (! Schema::hasTable('fgp_items')) {
        Schema::create('fgp_items', function (Blueprint $table) {
            $table->id('fgp_item_id');
            $table->unsignedBigInteger('category_ID')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('case_cost', 10, 2);
            $table->integer('internal_inventory');
            $table->json('dates_available')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->boolean('orderable')->default(1);
            $table->timestamps();

            // Ensure category_ID exists in fgp_categories
            $table->foreign('category_ID')->references('category_ID')->on('fgp_categories')->onDelete('cascade');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_items');
    }
};
