<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('fgp_items')) {
            Schema::create('fgp_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('franchise_id')->constrained('franchises');
                $table->foreignId('fgp_category_id')->constrained('fgp_categories');
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('case_cost', 10, 2);
                $table->integer('internal_inventory');
                $table->unsignedInteger('split_factor')->default(48);
                $table->json('dates_available')->nullable();
                $table->string('image1')->nullable();
                $table->string('image2')->nullable();
                $table->string('image3')->nullable();
                $table->boolean('orderable')->default(1);
                $table->foreignId('created_by')->constrained('users','id');
                $table->foreignId('updated_by')->constrained('users','id');
                $table->timestamps();
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
