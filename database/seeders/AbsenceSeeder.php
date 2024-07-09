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
        /*
        // Verificar se a tabela work_shifts já contém os dados antes de inserir
        if (DB::table('absences')->count() == 0) {
            DB::table('absences')->insert([
                [
                    'id' => 1,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-24 00:00:00',
                    'absence_end_date' => '2024-06-02 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 2,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-02 18:21:10',
                    'absence_end_date' => '2024-06-02 19:21:10',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                // Faltas adicionais
                [
                    'id' => 3,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-03 08:00:00',
                    'absence_end_date' => '2024-06-03 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 4,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-04 08:00:00',
                    'absence_end_date' => '2024-06-04 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 5,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-05 08:00:00',
                    'absence_end_date' => '2024-06-05 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 6,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-06 08:00:00',
                    'absence_end_date' => '2024-06-06 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 7,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-07 08:00:00',
                    'absence_end_date' => '2024-06-07 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 8,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-08 08:00:00',
                    'absence_end_date' => '2024-06-08 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 9,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-09 08:00:00',
                    'absence_end_date' => '2024-06-09 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 10,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-10 08:00:00',
                    'absence_end_date' => '2024-06-10 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 11,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-11 08:00:00',
                    'absence_end_date' => '2024-06-11 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 12,
                    'user_id' => 1,
                    'justification_id' => null,
                    'absence_states_id' => 4,
                    'absence_types_id' => 1,
                    'approved_by' => null,
                    'absence_start_date' => '2024-06-12 08:00:00',
                    'absence_end_date' => '2024-06-12 12:00:00',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
            ]);
        }*/
    }
}
