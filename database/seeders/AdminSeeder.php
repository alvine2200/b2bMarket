<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=User::create([

            'full_name' =>'System Admin',
            'admin_email' =>'Systemadmin@gmail.com',
            //'email_verified_at'=> now(),
            'password' => Hash::make('admin@2022'),
            'is_super_admin'=>'1',


        ],
        ["full_name","password","is_super_admin",'email']

    );


    }
}
