<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableBusinessProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_products')->upsert(
            [
                ["name" => "Financial Products (Loans, Savings)"],
                ["name" => "Mobile Apps"],
                ["name" => "Educational  Materials"],
                ["name" => "Farm Produce"],
                ["name" => "Vehicles"],
                ["name" => "Clothing"],
                ["name" => "Devices"],
                ["name" => "Equipment"],
                ["name" => "Real Estate "],
                ["name" => "Tour Packages"]
            ],
            ["name"]
        );
    }
}
