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
        if (! Schema::hasTable('expenses')) {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('franchisee_id');
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('franchisee_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
