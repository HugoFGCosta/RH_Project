<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    use HasFactory;

    protected $fillable = [
        'motive',
        'justification_date',
        'observation',
        'absence_id',
        'file'
    ];

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
