<?php

// database/migrations/2025_09_04_000003_create_purchases_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vgp_id')->index(); // id người chơi
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnDelete();

            $table->unsignedBigInteger('unit_amount'); // sao chép từ items.amount để thống kê nhanh
            $table->unsignedBigInteger('vnd_paid');
            $table->string('status')->default('success'); // success/pending/failed
            $table->string('external_txn_id')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->index(['vgp_id', 'game_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
