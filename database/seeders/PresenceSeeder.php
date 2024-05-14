<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Verificar se a tabela presences já contém os dados antes de inserir
        if (DB::table('presences')->count() == 0) {
            DB::table('presences')->insert([
                'user_id' => 1,
                'first_start' => '2024-05-08',
                'first_end' => '2024-05-08',
                'second_start' => '2024-05-08',
                'second_end' => '2024-05-08',
                'extra_hour' => 0,
                'effective_hour' => 0,
            ]);
        }
    }
}
