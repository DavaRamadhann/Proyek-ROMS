<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up(): void
    {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();

            // Foreign key aman PostgreSQL
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();

            $table->text('message_content');
            $table->string('type', 30)->default('reminder');  // reminder, broadcast, followup
            $table->string('status', 20)->default('pending'); // pending, sent, failed

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
