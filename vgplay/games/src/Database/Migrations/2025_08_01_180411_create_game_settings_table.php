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
        Schema::create('game_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->index();
            $table->string('required_vxu')->nullable();
            $table->string('description')->nullable();
            $table->string('content')->nullable();
            $table->string('homepage_url')->nullable();
            $table->string('appstore_url')->nullable();
            $table->string('google_play_url')->nullable();
            $table->string('apk_url')->nullable();
            $table->string('support_url')->nullable();
            $table->string('cdn_url')->nullable();
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
        Schema::dropIfExists('game_settings');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
