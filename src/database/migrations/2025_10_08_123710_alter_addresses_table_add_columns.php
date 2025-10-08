<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('addresses', 'postal')) {
                $table->string('postal', 8);
            }
            if (!Schema::hasColumn('addresses', 'prefecture')) {
                $table->string('prefecture', 64);
            }
            if (!Schema::hasColumn('addresses', 'city')) {
                $table->string('city', 128);
            }
            if (!Schema::hasColumn('addresses', 'line1')) {
                $table->string('line1', 255);
            }
            if (!Schema::hasColumn('addresses', 'line2')) {
                $table->string('line2', 255)->nullable();
            }
            if (!Schema::hasColumn('addresses', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            if (!Schema::hasColumn('addresses', 'is_default')) {
                $table->boolean('is_default')->default(false)->index();
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // 外部キー → カラム の順で落とす
            if (Schema::hasColumn('addresses', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            foreach (['postal', 'prefecture', 'city', 'line1', 'line2', 'phone', 'is_default'] as $col) {
                if (Schema::hasColumn('addresses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
