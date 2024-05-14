<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_start',
        'first_end',
        'second_start',
        'second_end',
        'extra_hour',
<<<<<<< HEAD
        'effective_hour',
=======
        'effective_hour'
>>>>>>> 7c5ede4a79865eb6065791e9fe4d9acd0749ad0c
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
