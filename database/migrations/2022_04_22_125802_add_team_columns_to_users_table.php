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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string("profile_image")->nullable();
            $table->string("quote")->nullable();
            $table->string("is_team_member")->default(false);
            $table->foreignId("teambusiness_id")->nullable()->constrained("businesses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn("profile_image");
            $table->dropColumn("quote");
            $table->dropColumn("is_team_member");
            $table->dropForeign("users_teambusiness_id_foreign");
            $table->dropColumn("teambusiness_id");
        });
    }
};
