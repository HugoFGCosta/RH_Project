<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work_Shift extends Model
{
    use HasFactory;

    protected $table = 'work_shifts';


    public function user_shift()
    {
        return $this->hasMany(User_Shift::class);
    }
}