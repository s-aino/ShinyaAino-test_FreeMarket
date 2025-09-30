<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                // unsigned bigint
            $table->string('name', 20);                  // 20文字
            $table->string('email', 255)->unique();      // 一意
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();                        // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};