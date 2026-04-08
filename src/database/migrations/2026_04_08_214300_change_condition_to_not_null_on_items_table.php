<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ① 既存のNULLを埋める（超重要）
        DB::table('items')
            ->whereNull('condition')
            ->update(['condition' => '未設定']);

        // ② NOT NULLに変更
        Schema::table('items', function (Blueprint $table) {
            $table->string('condition')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('condition')->nullable()->change();
        });
    }
};
