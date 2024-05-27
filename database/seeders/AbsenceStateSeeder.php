<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenceStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela roles já contém os dados antes de inserir
        if (DB::table('absence_states')->count() == 0) {
            DB::table('absence_states')->insert([
                ['id' => 1, 'description' => 'Aprovado'],
                ['id' => 2, 'description' => 'Rejeitado'],
                ['id' => 3, 'description' => 'Pendente']
            ]);
        }
    }
}
