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
        return $this->belongsTo('App\Work_Shift');
    }
    public function user()
    {
        return $this->belongsTo('App\Users');
    }
}