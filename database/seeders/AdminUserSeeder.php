<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\User_Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if (User::count() == 0) {
            $admin = User::factory()->admin()->create();

            User_Shift::create([
                'user_id' => $admin->id,  // usa o id do admin criado
                'work_shift_id' => 1,     // define o work_shift_id como 1
                'start_date' => now(),
                'end_date' => null
            ]);

        }
    }

}