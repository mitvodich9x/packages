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
        Schema::create('game_apis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->index();
            $table->text('api_config')->nullable();
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
        Schema::dropIfExists('game_apis');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
