<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'work_shift_id',
        'address',
        'nif',
        'tel',
        'birth_date'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role_id == 3; // Verifica se o utilizador é um administrador
    }

    public function isManager()
    {
        return $this->role_id == 2; // Verifica se o utilizador é um gestor
    }

    public function isWorker()
    {
        return $this->role_id == 1; // Verifica se o utilizador é um trabalhador
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }

    public function user_shifts()
    {
        return $this->hasMany(User_Shift::class);
    }


    public function event()
    {
        return $this->belongsTo(User::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
