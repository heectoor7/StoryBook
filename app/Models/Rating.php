<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    // ratings table only has created_at, no updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'service_id',
        'rating',
        'comment',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}