<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    public function vacation_approval_state()
    {
        return $this->belongsTo('App\Vacation_Approval_state');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}