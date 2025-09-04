<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('vgp_id')->index();
            $table->unsignedBigInteger('game_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('vxu_amount')->default(0);
            $table->unsignedBigInteger('price_vnd')->default(0);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->index();
            $table->string('external_trx_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('game_id')->references('game_id')->on('games')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();

            $table->index(['vgp_id', 'game_id', 'item_id']);
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('purchase_histories');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
