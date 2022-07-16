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
            $table->boolean("is_business_rep")->default(false);
            $table->boolean("is_investor")->default(false);
            $table->boolean("is_employee")->default(false);
            $table->boolean("is_super_admin")->default(false);
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
            $table->dropColumn("is_business_rep");
            $table->dropColumn("is_investor");
            $table->dropColumn("is_employee");
            $table->dropColumn("is_super_admin");
        });
    }
};
