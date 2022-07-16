<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableBusinessKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_keywords')->upsert([
            ["type"=>"country", "name"=>"Kenya", "is_suggested"=>false],
            ["type"=>"country", "name"=>"Ghana", "is_suggested"=>false],
            ["type"=>"products", "name"=>"Software", "is_suggested"=>false],
            ["type"=>"products", "name"=>"Equipment", "is_suggested"=>false],
            ["type"=>"services_interested_or_dealing", "name"=>"Delivery", "is_suggested"=>false],
            ["type"=>"services_interested_or_dealing", "name"=>"Software", "is_suggested"=>false],
            ["type"=>"technologies_interested_in", "name"=>"Javascript", "is_suggested"=>false],
            ["type"=>"technologies_interested_in", "name"=>"PHP", "is_suggested"=>false],
            ["type"=>"value_chains", "name"=>"Industry Level", "is_suggested"=>false],
            ["type"=>"value_chains", "name"=>"Global", "is_suggested"=>false],
        ], ["type", "name", "is_suggested"]);
    }
}
