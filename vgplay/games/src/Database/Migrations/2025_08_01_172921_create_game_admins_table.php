<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('game_admins');

        Schema::create('game_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->index();
            $table->string('name')->nullable();
            $table->string('desc')->nullable();
            $table->string('avatar')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('zalo_url')->nullable();
            $table->string('telegram')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('game_admins');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
