<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SelectableBusinessInterestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('selectable_business_interests')->upsert([
            ["type"=> "scale_business_partnership_location", "name"=> "In my country", "is_suggested"=> false],
            ["type"=> "scale_business_partnership_location", "name"=> "Across Africa", "is_suggested"=> false],
            ["type"=> "commercial_opportunities", "name"=> "Selling/supplying my products/services", "is_suggested"=> false],
            ["type"=> "commercial_opportunities", "name"=> "Purchasing products/services", "is_suggested"=> false],
            ["type"=> "distribution_opportunities", "name"=> "My business can become a distributor for another business.", "is_suggested"=> false],
            ["type"=> "distribution_opportunities", "name"=> "My business is looking for a distributor.", "is_suggested"=> false],
            ["type"=> "import_export_opportunities", "name"=> "In Africa", "is_suggested"=> false],
            ["type"=> "import_export_opportunities", "name"=> "Overseas", "is_suggested"=> false],
            ["type"=> "consulting_services", "name"=> "My business can offer consulting services.", "is_suggested"=> false],
            ["type"=> "consulting_services", "name"=> "My business needs consulting services.", "is_suggested"=> false],
            ["type"=> "investing", "name"=> "My business is looking for investors.", "is_suggested"=> false],
            ["type"=> "investing", "name"=> "My business could invest in other companies.", "is_suggested"=> false],
        ], ["type", "name", "is_suggested"]);
    }
}
