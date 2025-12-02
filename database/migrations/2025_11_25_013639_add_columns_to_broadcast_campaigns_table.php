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
        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->integer('total_recipients')->default(0)->after('status');
            $table->integer('success_count')->default(0)->after('total_recipients');
            $table->integer('fail_count')->default(0)->after('success_count');
            $table->unsignedBigInteger('created_by')->nullable()->after('fail_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->dropColumn(['total_recipients', 'success_count', 'fail_count', 'created_by']);
        });
    }
};
