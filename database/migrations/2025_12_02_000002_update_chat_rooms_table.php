<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('chat_contact_id')->nullable()->after('customer_id');
            
            $table->foreign('chat_contact_id')->references('id')->on('chat_contacts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropForeign(['chat_contact_id']);
            $table->dropColumn('chat_contact_id');
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }
};
