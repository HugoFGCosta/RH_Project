<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Work_Shift extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'start_hour',
        'break_start',
        'break_end',
        'end_hour',
    ];

    protected $table = 'work_shifts';


    public function user_shifts()
    {
        return $this->hasMany(User_Shift::class);
    }
}
