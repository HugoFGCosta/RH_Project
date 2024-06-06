<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User_Shift extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'work_shift_id',
        'start_date',
        'end_date',
    ];

    protected $table = 'user_shifts';
    public function work_shift()
    {
        return $this->belongsTo(Work_Shift::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}