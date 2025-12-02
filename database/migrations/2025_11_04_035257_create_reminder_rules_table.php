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
        Schema::create('reminder_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message_template');
            $table->integer('days_after_shipped');
            $table->boolean('is_active')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_rules');
    }
};
