<?php

// database/migrations/2025_09_04_000002_create_items_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // "Gói 200 Vxu"
            $table->string('code')->unique();    // "VXU_200"
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('vxu');      // vxu, knb, ...
            $table->string('unit')->default('Vxu'); // nhãn hiển thị

            // "Số lượng đơn vị" người chơi nhận khi mua (dùng để map qua game_payment_rates)
            $table->unsignedBigInteger('amount');    // 200, 500, 1000...

            $table->unsignedInteger('discount')->default(0); // % nếu cần
            $table->text('details')->nullable();     // [{image,name,desc}, ...]

            // Phạm vi hiển thị
            $table->boolean('is_global')->default(true)->index(); // gói VXU = true; KNB = false (chỉ vài game)

            // Rule mua
            $table->boolean('allow_multiple')->default(false);       // bật/tắt mua nhiều lần
            $table->unsignedInteger('limit_per_user')->nullable();  // null = không giới hạn; 1 = mua 1 lần

            // Mở khoá gói to: yêu cầu đã mua tối thiểu N gói "nhỏ hơn hoặc bằng một ngưỡng"
            $table->unsignedInteger('unlock_min_buys')->default(0);      // cần mua tối thiểu N lần
            $table->unsignedBigInteger('unlock_price_ceiling')->nullable(); // chỉ tính những gói có amount <= ceiling (mặc định = amount hiện tại - 1 nếu null)

            $table->boolean('active')->default(true)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Pivot: item nào thuộc game nào (cho các gói KHÔNG global)
        Schema::create('game_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unique(['game_id', 'item_id']);

            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_item');
        Schema::dropIfExists('items');
    }
};
