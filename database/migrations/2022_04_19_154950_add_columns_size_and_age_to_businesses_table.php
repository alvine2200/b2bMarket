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
        Schema::table('businesses', function (Blueprint $table) {
            //
            $table->integer("size_start_range")->nullable();
            $table->integer("size_end_range")->nullable();
            $table->integer("age_start_range")->nullable();
            $table->integer("age_end_range")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            //
            $table->dropColumn("size_start_range");
            $table->dropColumn("size_end_range");
            $table->dropColumn("age_start_range");
            $table->dropColumn("age_end_range");
        });
    }
};
