<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scheduled_social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->nullable()->constrained('franchises');
            $table->string('meta_page_id');
            $table->text('access_token');
            $table->text('message');
            $table->timestamp('scheduled_for');
            $table->boolean('posted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('scheduled_social_posts');
    }
};
