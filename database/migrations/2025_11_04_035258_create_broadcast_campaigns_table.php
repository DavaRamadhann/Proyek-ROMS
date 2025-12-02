<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up(): void
    {
        Schema::create('broadcast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message_content');
            $table->string('target_segment', 100)->nullable(); // contoh: loyal, inactive, all
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status', 20)->default('draft'); // draft, scheduled, sent
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_campaigns');
    }
};
