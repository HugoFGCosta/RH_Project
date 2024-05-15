<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Shift extends Model
{
    use HasFactory;

    protected $table = 'user_shifts';
    public function work_shift()
    {
        return $this->belongsTo(Work_Shift::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
