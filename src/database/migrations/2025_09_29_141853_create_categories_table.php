<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // unsigned bigint
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->timestamps(); // created_at, updated_at
        });
    }
    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
