<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            // Foreign key aman PostgreSQL
            $table->foreignId('chat_room_id')
                  ->constrained('chat_rooms')
                  ->cascadeOnDelete();

            // sender_id bersifat fleksibel (bisa user / customer / system)
            // Tidak dibuat foreign key karena bersifat polymorphic manual
            $table->unsignedBigInteger('sender_id')->nullable();

            $table->string('sender_type'); // customer, user, system
            $table->text('message_content');
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed

            // created_at custom (tanpa updated_at)
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
