<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_room_user', function (Blueprint $table) {
            //
            $table->dateTime("cleared_at")->nullable();
            $table->dateTime("deleted_at")->nullable();
            $table->dateTime("archived_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_room_user', function (Blueprint $table) {
            //
            $table->dropColumn("cleared_at");
            $table->dropColumn("deleted_at");
            $table->dropColumn("archived_at");
        });
    }
};
