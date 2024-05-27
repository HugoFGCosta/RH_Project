<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela roles já contém os dados antes de inserir
        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert([
                ['id' => 1,
                    'name' => 'luis',
                    'address' => 'Rua do luis',
                    'nif' => '123456789',
                    'tel' => '123456789',
                    'birth_date' => '2024-05-08',
                    'email' => 'luis@email.com',
                    'password' => 'password',
                    'role_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
