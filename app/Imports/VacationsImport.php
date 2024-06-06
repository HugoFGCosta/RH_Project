<?php

namespace App\Imports;

use App\Models\Vacation;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;

class VacationsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Vacation([
            //
            'id' => $row[0],
            'user_id' => $row[1],
            'vacation_approval_states_id' => $row[2],
            'approved_by' => $row[3],
            'date_start' => $row[4],
            'date_end' => $row[5],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
