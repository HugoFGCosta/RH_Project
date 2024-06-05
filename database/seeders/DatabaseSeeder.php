<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AbsenceTypeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(WorkShiftSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(AbsenceStateSeeder::class);
        $this->call(VacationApprovalStateSeeder::class);
        $this->call(AbsenceSeeder::class);
        /**  Seeder de teste
        $this->call(UserSeeder::class); */
    }
}
