<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role =Role::upsert([

            ['name' => 'admin', 'guard_name'=>'web'],
            ['name' => 'user', 'guard_name'=>'web'],

        ],

        ['name'],['guard_name'],['guard_name']);
    }
}
