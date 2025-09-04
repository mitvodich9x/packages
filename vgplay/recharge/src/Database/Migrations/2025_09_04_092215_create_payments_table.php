<?php

// database/migrations/2025_09_04_000001_create_payment_methods_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('alias')->unique()->index(); // atm, visa, momo, zalopay, googlepay, samsungpay, applepay, qr
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->json('promotion')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Bảng cấu hình mệnh giá theo game + phương thức
        Schema::create('game_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->unsignedBigInteger('unit_amount'); // 200, 500, 1000, ...
            $table->unsignedBigInteger('vnd');         // 20000, 52500, 110000, ...
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['game_id', 'payment_id', 'unit_amount']);
            $table->index(['game_id', 'unit_amount']);

            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_payments');
        Schema::dropIfExists('payments');
    }
};
