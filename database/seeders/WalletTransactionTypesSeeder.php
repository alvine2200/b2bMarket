<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class WalletTransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('wallet_transaction_types')->upsert([
            ['name' => 'DEPOSIT'],
            ['name' => 'WITHDRAW'],
            ['name' => 'RECEIVED_PAYMENT'],
            ['name' => 'SENT_PAYMENT'],
        ],
        ['name']
    );
    }
}
