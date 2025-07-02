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

        // Category-Item pivot table
        if (! Schema::hasTable('fgp_category_fgp_item')) {
            Schema::create('fgp_category_fgp_item', function (Blueprint $table) {
                $table->foreignId('fgp_category_id')->constrained('fgp_categories')->onDelete('cascade');
                $table->foreignId('fgp_item_id')->constrained('fgp_items')->onDelete('cascade');
                $table->primary(['fgp_category_id', 'fgp_item_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fgp_items');
        Schema::dropIfExists('fgp_category_fgp_item');
    }
};
