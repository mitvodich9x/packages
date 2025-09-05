<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('vxu')->index();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('image')->nullable();
            $table->string('unit')->default('vxu');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('vxu_amount')->default(0);
            $table->decimal('discount_percent', 6, 2)->default(0);
            $table->unsignedInteger('limit_per_user')->default(0);
            $table->boolean('allow_multiple_per_order')->default(true);
            $table->unsignedInteger('tier')->default(1)->index();
            $table->unsignedInteger('requires_min_tier')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
