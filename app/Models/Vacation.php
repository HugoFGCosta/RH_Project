<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vacation_approval_states_id',
        'approved_by',
        'date_start',
        'date_end',
    ];


    public function vacation_approval_state()
    {
        return $this->belongsTo(Vacation_Approval_State::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }
}
