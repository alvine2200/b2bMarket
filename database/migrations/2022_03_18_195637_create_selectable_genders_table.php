<?php

use App\Models\Selectables\SelectableGender;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selectable_genders', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("symbol")->nullable();
            $table->timestamps();
        });

        SelectableGender::upsert([

            ['name' => 'male', 'symbol'=>'M'],
            ['name' => 'female', 'symbol'=>'F'],
            ['name' => 'others', 'symbol'=> null],

        ], ['name', 'symbol']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selectable_genders');
    }
};
