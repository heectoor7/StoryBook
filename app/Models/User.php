<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    // ROLES
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // EMPRESA (si es company)
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    // RESERVAS
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // COMENTARIOS
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}