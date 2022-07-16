<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableInvestorTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_investor_types')->upsert(
            [
                ["name"=> "Angel Investors"],
                ["name"=> "Peer to Peer Lenders"],
                ["name"=> "Personal Investors"],
                ["name"=> "Banks"],
                ["name"=> "Venture Capitalists"]
            ],
            ["name"]
        );
    }
}
