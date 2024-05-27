<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Verificar se a tabela roles já contém os dados antes de inserir
        if (DB::table('roles')->count() == 0) {
            DB::table('roles')->insert([
                ['id' => 1, 'role' => 'Worker'],
                ['id' => 2, 'role' => 'Manager'],
                ['id' => 3, 'role' => 'Administrator']
            ]);
        }
    }
}
