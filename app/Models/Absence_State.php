<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence_State extends Model
{
    use HasFactory;

    protected $fillable = [
        'description'
    ];

    protected $table = 'absence_states';


    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}
