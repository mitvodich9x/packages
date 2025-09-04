<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('game_payment_method', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->decimal('exchange_rate', 12, 2)->default(100.00);
            $table->unsignedBigInteger('min_amount')->default(0);   // VND
            $table->unsignedBigInteger('max_amount')->default(0);   // VND (0 = tắt)
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->foreign('game_id')->references('game_id')->on('games')->cascadeOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->cascadeOnDelete();

            $table->unique(['game_id', 'payment_method_id']);
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('game_payment_method');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
