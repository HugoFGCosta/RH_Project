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
<<<<<<< LuisBranch
        'effective_hour',
    ];
=======
        'effective_hour'
    ];

>>>>>>> MergeTeste
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
