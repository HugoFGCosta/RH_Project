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
    }
}