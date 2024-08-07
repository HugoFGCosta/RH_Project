<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id',
        'justification_id',
        'absence_states_id',
        'absence_types_id',
        'approved_by',
        'absence_start_date',
        'absence_end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function absence_state()
    {
        return $this->belongsTo(Absence_State::class);
    }

    public function absence_type()
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function justification()
    {
        return $this->belongsTo(Justification::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }

}
