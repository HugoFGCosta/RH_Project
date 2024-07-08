<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se a tabela work_shifts já contém os dados antes de inserir
        if (DB::table('work_shifts')->count() == 0) {
            DB::table('work_shifts')->insert([
                [
                    'id' => 1,
                    'start_hour' => '08:00',
                    'break_start' => '12:00',
                    'break_end' => '13:00',
                    'end_hour' => '17:00'
                ],
                [
                    'id' => 2,
                    'start_hour' => '12:00',
                    'break_start' => '14:00',
                    'break_end' => '15:00',
                    'end_hour' => '20:00'
                ],
                [
                    'id' => 3,
                    'start_hour' => '14:00',
                    'break_start' => '18:00',
                    'break_end' => '19:00',
                    'end_hour' => '23:00'
                ],
                [
                    'id' => 4,
                    'start_hour' => '22:00',
                    'break_start' => '02:00',
                    'break_end' => '03:00',
                    'end_hour' => '06:00'
                ]

            ]);
        }
    }
}
