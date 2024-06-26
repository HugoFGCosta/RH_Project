<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation_Approval_State extends Model
{
    use HasFactory;

    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }
}