<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'address',
        'city',
        'phone',
        'verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function followers()
    {
        return $this->hasMany(Follower::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
