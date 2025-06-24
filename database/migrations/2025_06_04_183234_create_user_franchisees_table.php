<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_franchisees', function (Blueprint $table) {
            // $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('franchisee_id');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('franchisee_id')->references('franchisee_id')->on('franchisees')->onDelete('cascade');
            $table->unique(['user_id', 'franchisee_id']);
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



    public function down()
    {
        Schema::dropIfExists('user_franchisees');
    }

};
