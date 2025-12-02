<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public static $useTransactions = false;
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('google_id')->nullable()->unique();
            $table->string('role')->default('admin');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->string('reset_code', 6)->nullable();
            $table->timestamp('reset_code_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
