<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_item', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('game_id');

            $table->unsignedBigInteger('item_id');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->foreign('game_id')->references('game_id')->on('games')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();

            $table->unique(['game_id', 'item_id']);
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('game_item');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
