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
        Schema::dropIfExists('games');

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id')->unique()->index();
            $table->string('name')->index();
            $table->string('alias')->unique()->index();
            $table->string('banner')->nullable();
            $table->string('favicon')->nullable();
            $table->string('icon')->nullable();
            $table->string('logo')->nullable();
            $table->string('thumb')->nullable();
            $table->string('bg_detail')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('games');
        Schema::dropIfExists('items');
        Schema::dropIfExists('game_admins');
        Schema::dropIfExists('game_settings');
        Schema::dropIfExists('game_apis');
        Schema::dropIfExists('game_flags');
        Schema::dropIfExists('game_socials');
        Schema::dropIfExists('game_item');
        Schema::dropIfExists('purchase_histories');
        Schema::dropIfExists('game_payment_method');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
