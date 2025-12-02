<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();

            // Produk spesifik (nullable = bisa berlaku untuk semua produk)
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->string('name');                   // Nama reminder (misal: "Reminder Biji Kopi")
            $table->integer('days_after_delivery');   // Berapa hari setelah pesanan delivered
            $table->text('message_template');         // Template pesan WhatsApp
            $table->boolean('is_active')->default(true); // ON/OFF reminder
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
