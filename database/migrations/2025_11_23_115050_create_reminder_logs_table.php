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
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_id')->constrained()->onDelete('cascade'); // Reminder rule yang digunakan
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Order yang di-remind
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Customer penerima
            $table->timestamp('scheduled_at'); // Kapan seharusnya dikirim
            $table->timestamp('sent_at')->nullable(); // Kapan benar-benar dikirim
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending'); // Status pengiriman
            $table->text('message_sent')->nullable(); // Pesan yang dikirim (setelah replace variable)
            $table->text('error_message')->nullable(); // Error jika gagal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
