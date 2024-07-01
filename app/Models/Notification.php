<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'absence_id',
        'vacation_id',
        'events_id',
        'state',
    ];

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }

    public function vacation()
    {
        return $this->belongsTo(Vacation::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'events_id');
    }
}