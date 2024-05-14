<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            //
            'role_id' => $row[1],
            'name' => $row[2],
            'address' => $row[3],
            'nif'=> $row[4],
            'tel'=> $row[5],
            'birth_date'=> $row[6],
            'email'=> $row[7],
            'password'=> $row[9],
            'created_at'=> now(),
            'updated_at'=> now(),

        ]);
    }
}
