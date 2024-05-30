<?php

namespace App\Imports;

use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class AbsencesImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Absence([
            'id' => $row[0],
            'user_id' => $row[1],
            'absence_states_id' => $row[2],
            'approved_by' => $row[3],
            'absence_date' => $row[4],
            'justification' => $row[5],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}

