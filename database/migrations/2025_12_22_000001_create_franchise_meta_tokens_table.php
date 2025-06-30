<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('franchise_meta_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises');
            $table->string('meta_page_id');
            $table->text('meta_access_token');
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('franchise_meta_tokens');
    }
};
