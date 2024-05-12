<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('work_shifts')->insert([
            'id' => 1,
            'start_hour' => '08:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'end_hour' => '17:00'
        ]);

        \DB::table('work_shifts')->insert([
            'id' => 2,
            'start_hour' => '12:00',
            'break_start' => '14:00',
            'break_end' => '15:00',
            'end_hour' => '20:00'
        ]);
    }
}