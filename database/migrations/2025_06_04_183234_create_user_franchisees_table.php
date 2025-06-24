<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_franchises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('franchise_id');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('franchise_id')->references('franchise_id')->on('franchises')->onDelete('cascade');
        });

        // ✅ Only insert if franchisee_id exists
        // $users = DB::table('users')->get(['user_id', 'franchisee_id']);

        // foreach ($users as $user) {
        //     if ($user->franchisee_id) {
        //         DB::table('user_franchisees')->insert([
        //             'user_id' => $user->user_id,
        //             'franchisee_id' => $user->franchisee_id,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_franchises');
    }
};
