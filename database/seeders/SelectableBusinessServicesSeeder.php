<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableBusinessServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_services')->upsert(
            [
                ["name" => "Farming"],
                ["name" => "Fashion"],
                ["name" => "Recycling"],
                ["name" => "Investment"],
                ["name" => "Financial Services"],
                ["name" => "Consultancy"],
                ["name" => "Training"],
                ["name" => "Communication"],
                ["name" => "Software Development"],
                ["name" => "Import and Exports"],
                ["name" => "Health Services"],
                ["name" => "Logistics"],
                ["name" => "Distribution"],
                ["name" => "Transportation"],
                ["name" => "Manufacturing"],
                ["name" => "Real Estate"],
                ["name" => "Tourism"]
            ],
            ["name"]
        );
    }
}
