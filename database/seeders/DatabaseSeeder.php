<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            SelectableBusinessInterestsSeeder::class,
            SelectableBusinessSectorsSeeder::class,
            SelectableBusinessKeywordsSeeder::class,
            PaymentMethodSeeder::class,
            ShippingMethodSeeder::class,
            SelectableBusinessRolesSeeder::class,
            SelectableInvestorTypesSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
