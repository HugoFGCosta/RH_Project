<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'absence_states_id',
        'approved_by',
        'absence_date',
        'justification',
    ];

    public function absence_state()
    {
        return $this->belongsTo(Absence_State::class);
    }
}