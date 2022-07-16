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
        Schema::create('advanced_filter_recent_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('size_start_range')->nullable();
            $table->string('size_end_range')->nullable();
            $table->string('age_start_range')->nullable();
            $table->string('age_end_range')->nullable();
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
        Schema::dropIfExists('advanced_filter_recent_searches');
    }
};
