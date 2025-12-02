<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up()
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();

            // Foreign keys - versi aman PostgreSQL
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();

            $table->foreignId('cs_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('status')->default('new'); // new, open, closed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
};
