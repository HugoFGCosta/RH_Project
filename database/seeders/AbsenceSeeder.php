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

        // Verificar se a tabela work_shifts já contém os dados antes de inserir
        /* if (DB::table('absences')->count() == 0) {
            DB::table('absences')->insert([
                [
                    'id' => 1,
                    'user_id' => 1,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-02 18:21:10',
                    'absence_end_date' => '2024-06-02 19:21:10',
                    'justification' => 'Justificação de teste',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 1,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-02 18:21:10',
                    'absence_end_date' => '2024-06-02 19:21:10',
                    'justification' => 'Justificação de teste',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            ]); 
        }*/
            ]);
    }
}