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
        Schema::create('csv_import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('template_type'); // contoh: 'customer', 'product'
            $table->string('status', 20)->default('processing'); // success, failed
            $table->integer('processed_rows')->default(0);
            $table->string('error_log_file')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_import_logs');
    }
};
