<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->upsert([

            ['name'=>'Cash On Delivery'],
            ['name'=>'cheque'],
            ['name'=>'paypal'],

          ],
          ['name'],
        );
        }
}
