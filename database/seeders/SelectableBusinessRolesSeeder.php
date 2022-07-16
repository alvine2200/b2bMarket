<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectableBusinessRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_roles')->upsert([
            ["name"=> "CEO"],
            ["name"=> "CTO"],
            ["name"=> "CFO"],
            ["name"=> "CMO"],
            ["name"=> "CPO"],
            ["name"=> "COO"],
            ["name"=> "CLO"],
            ["name"=> "Manager"],
            ["name"=> "Accountant"],
            ["name"=> "Human Resources Manager"],
            ["name"=> "Customer Service"],
            ["name"=> "Sales"],
            ["name"=> "Marketing"],
            ["name"=> "Financial Analyst"],
            ["name"=> "IT"],
        ],["name"]);
    }
}
