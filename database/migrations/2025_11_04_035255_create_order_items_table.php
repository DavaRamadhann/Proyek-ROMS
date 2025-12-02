<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public static $useTransactions = false;

    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Foreign key aman untuk PostgreSQL
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
