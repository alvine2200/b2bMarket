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
            $table->foreignId('business_role_id')->nullable()->constrained('selectable_business_roles');
            $table->foreignId('gender_id')->nullable()->constrained('selectable_genders');
            $table->foreignId('nationality_id')->nullable()->constrained('selectable_countries');
            $table->foreignId('sector_id')->nullable()->constrained('selectable_business_sectors');
            $table->foreignId('investor_type_id')->nullable()->constrained('selectable_investor_types');
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
            $table->dropForeign('users_business_role_id_foreign');
            $table->dropColumn('business_role_id');

            $table->dropForeign('users_gender_id_foreign');
            $table->dropColumn('gender_id');

            $table->dropForeign('users_nationality_id_foreign');
            $table->dropColumn('nationality_id');
            
            $table->dropForeign('users_sector_id_foreign');
            $table->dropColumn('sector_id');
            
            $table->dropForeign('users_investor_type_id_foreign');
            $table->dropColumn('investor_type_id');
            
        });
    }
};
