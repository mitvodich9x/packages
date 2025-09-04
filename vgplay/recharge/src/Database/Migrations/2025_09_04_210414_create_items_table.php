<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('vxu')->index(); // vxu, knb, ...
            $table->string('name');
            $table->string('code')->unique()->index();
            $table->string('image')->nullable();
            $table->string('unit')->default('vxu'); // "vxu", "knb"
            $table->text('description')->nullable();

            // giá theo đơn vị "unit" (vd 200 vxu)
            $table->unsignedBigInteger('vxu_amount')->default(0);

            // chiết khấu hiển thị (%), tính khi render
            $table->decimal('discount_percent', 6, 2)->default(0); 

            // giới hạn mua: 0 = không giới hạn, >0 = tối đa / user
            $table->unsignedInteger('limit_per_user')->default(0);

            // Cho phép mua nhiều lần trong 1 đơn? (tuỳ nghiệp vụ, để true mặc định)
            $table->boolean('allow_multiple_per_order')->default(true);

            // Tier & điều kiện mở khóa (ví dụ: gói lớn tier=4, requires_min_tier=3)
            $table->unsignedInteger('tier')->default(1)->index();
            $table->unsignedInteger('requires_min_tier')->default(0)->index();

            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
