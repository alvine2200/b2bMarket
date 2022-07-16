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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string("slug")->unique();
            $table->string("name")->unique();
            $table->string("website")->nullable()->unique();
            $table->string("email")->unique();
            $table->integer("registration_step")->default(1);
            $table->foreignId("headquarters_id")->nullable()->constrained("selectable_countries");
            $table->string("phone")->nullable()->unique();
            $table->string('business_type')->nullable();
            $table->foreignId("main_sector_id")->nullable()->constrained("selectable_business_sectors");
            $table->string("incorporation_number")->nullable()->unique();
            $table->string("clients")->nullable();
            $table->json("platform_needs")->nullable();
            $table->foreignId("venture_sector_id")->nullable()->constrained("selectable_business_sectors");
            $table->json("expand_services")->nullable();
            $table->text("executive_summary")->nullable();
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
        Schema::dropIfExists('businesses');
    }
};
