<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        \DB::table('roles')->insert([
            'id' => 1,
            'role' => 'Worker'
        ]);

        \DB::table('roles')->insert([
            'id' => 2,
            'role' => 'Manager'
        ]);

        \DB::table('roles')->insert([
            'id' => 3,
            'role' => 'Administrator'
        ]);
    }
}