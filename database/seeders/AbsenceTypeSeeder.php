<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbsenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela absence_types já contém os dados antes de inserir
        if (DB::table('absence_types')->count() == 0) {
            DB::table('absence_types')->insert([
                [
                    'id' => 1,
                    'description' => 'First Shift',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 2,
                    'description' => 'Second Shift',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ],
                [
                    'id' => 3,
                    'description' => 'Total',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]
            ]);
        }
    }
}
