<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_methods')->upsert([

            ['name' => 'By Plane, if Inter_Continental'],
            ['name' => 'By Bus, if Within_Country'],
        ],
        ['name']
    );
    }
}
