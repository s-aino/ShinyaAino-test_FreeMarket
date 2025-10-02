<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            // ユーザー消したら住所も消す
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('postal', 8);
            $table->string('prefecture', 64);
            $table->string('city', 128);
            $table->string('line1', 255);
            $table->string('line2', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_default')->default(false);

            $table->timestamps(); // ★ A案：updated_at を持つ
            $table->index(['user_id', 'is_default']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
