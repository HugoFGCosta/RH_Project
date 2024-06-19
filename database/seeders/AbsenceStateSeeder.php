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
        // Verificar se a tabela work_shifts já contém os dados antes de inserir
        if (DB::table('absence_states')->count() == 0) {
            DB::table('absence_states')->insert([
                [
                    'id' => 1,
                    'description' => 'Aprovado',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 2,
                    'description' => 'Rejeitado',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 3,
                    'description' => 'Pendente',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 4,
                    'description' => 'Injustificado',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 5,
                    'description' => 'Injustificado Permanentemente',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],


            ]);
        }
    }
}
