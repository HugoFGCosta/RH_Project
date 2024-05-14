<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela vacations já contém os dados antes de inserir
        if (DB::table('vacations')->count() == 0) {
            DB::table('vacations')->insert([
                ['id' => 1,
                    'user_id' => 1,
                    'vacation_approval_states_id' => 1,
                    'approved_by' => 1,
                    'date_start' => '2024-05-08',
                    'date_end' => '2024-05-08',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
