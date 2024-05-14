<?php

namespace App\Imports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;

class PresencesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Presence([
            //
            'user_id' => $row[1],
            'first_start' => $row[2],
            'first_end' => $row[3],
            'second_start' => $row[4],
            'second_end' => $row[5],
            'extra_hour' => $row[6],
            'effective_hour' => $row[7],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
