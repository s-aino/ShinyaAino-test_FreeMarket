<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            // 出品者：ユーザー削除時は出品も消す（要件に合わせて変更可）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // カテゴリ：カテゴリに紐づく商品があると削除不可（安全策）
            $table->foreignId('category_id')->constrained()->restrictOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // 0 以上
            $table->enum('status', ['active', 'sold'])->default('active');
            $table->string('image_path', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'category_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('items');
    }
};
