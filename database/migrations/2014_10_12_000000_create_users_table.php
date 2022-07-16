<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->unique();
            $table->string('password');
            $table->string('slug')->unique();
            // $table->string('first_name');
            // $table->string('middle_name')->nullable();
            // $table->string('last_name');
            // $table->string('email')->unique();
            // $table->string('secondary_email')->nullable();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('phone', 17)->unique();
            // $table->string('secondary_phone', 17);
            // $table->string('business_type');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
