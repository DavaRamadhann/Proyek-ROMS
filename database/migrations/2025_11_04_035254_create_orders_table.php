<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            // Foreign key aman PostgreSQL
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();

            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
