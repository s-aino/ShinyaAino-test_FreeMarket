<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 購入者：注文があるユーザーは基本削除不可（履歴保持のため）
            $table->foreignId('buyer_id')->constrained('users')->restrictOnDelete();
            // 対象商品：注文がある商品は削除不可（履歴保持）
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            // 配送先：注文がある住所は削除不可（履歴保持）
            $table->foreignId('address_id')->constrained()->restrictOnDelete();

            $table->unsignedInteger('price');
            $table->unsignedInteger('qty')->default(1);
            $table->enum('status', ['pending', 'paid', 'canceled'])->default('pending');
            $table->dateTime('ordered_at');

            $table->timestamps();

            $table->index(['buyer_id', 'item_id', 'status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
