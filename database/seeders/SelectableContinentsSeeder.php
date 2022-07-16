<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableContinentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_continents')->upsert(
            [
                ["name"=>"Africa", "code2"=>"AF"],
                ["name"=>"Antarctica", "code2"=>"AN"],
                ["name"=>"Asia", "code2"=>"AS"],
                ["name"=>"Europe", "code2"=>"EU"],
                ["name"=>"North America", "code2"=>"NA"],
                ["name"=>"Oceania", "code2"=>"OC"],
                ["name"=>"South America", "code2"=>"SA"],
            ],
            ["name", "code2"]);
    }
}
