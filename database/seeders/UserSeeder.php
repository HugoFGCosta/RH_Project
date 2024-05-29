<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User_Shift;
use App\Models\Work_Shift;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**  Seeder de teste

        User::factory()->count(50)->create()->each(function ($user) {
            User_Shift::create([
                'user_id' => $user->id,
                'work_shift_id' => Work_Shift::all()->random()->id,
                'start_date' => now(),
                'end_date' => null,
            ]);
        }); */
    }
}
