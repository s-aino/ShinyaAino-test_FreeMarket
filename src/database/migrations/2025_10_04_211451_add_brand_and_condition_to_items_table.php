<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'brand')) {
                $table->string('brand')->nullable()->after('price');
            }
            if (!Schema::hasColumn('items', 'condition')) {
                $table->string('condition')->nullable()->after('brand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'condition')) {
                $table->dropColumn('condition');
            }
            if (Schema::hasColumn('items', 'brand')) {
                $table->dropColumn('brand');
            }
        });
    }
};
