<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela roles já contém os dados antes de inserir
        if (DB::table('absences')->count() == 0) {
            DB::table('absences')->insert([
                ['id' => 1,
                    'user_id' => 1,
                    'absence_states_id' => 1,
                    'approved_by' => 1,
                    'absence_date' => '2024-05-08',
                    'justification' => 'Sick',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
