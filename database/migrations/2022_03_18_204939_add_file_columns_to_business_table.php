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
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('certificate_of_incorporation')->nullable();
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
            $table->dropForeign('businesses_logo_file_id_foreign');
            $table->dropColumn('logo_file_id');

            $table->dropForeign('businesses_banner_file_id_foreign');
            $table->dropColumn('banner_file_id');
            
            $table->dropForeign('businesses_certificate_of_incorporation_file_id');
            $table->dropColumn('certificate_of_incorporation_file_id');
        });
    }
};
