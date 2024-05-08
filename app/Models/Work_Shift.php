<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work_Shift extends Model
{
    use HasFactory;



    public function user_shift()
    {
        return $this->hasOne('App\User_Shift');
    }
}