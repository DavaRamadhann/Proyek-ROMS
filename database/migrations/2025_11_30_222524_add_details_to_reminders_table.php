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
        Schema::table('reminders', function (Blueprint $table) {
            if (!Schema::hasColumn('reminders', 'send_time')) {
                $table->time('send_time')->default('09:00:00')->after('days_after_delivery');
            }
            if (!Schema::hasColumn('reminders', 'cross_sell_product_id')) {
                $table->foreignId('cross_sell_product_id')->nullable()->after('product_id')->constrained('products')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            if (Schema::hasColumn('reminders', 'send_time')) {
                $table->dropColumn('send_time');
            }
            if (Schema::hasColumn('reminders', 'cross_sell_product_id')) {
                $table->dropForeign(['cross_sell_product_id']);
                $table->dropColumn('cross_sell_product_id');
            }
        });
    }
};
