<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'role',
        'email',
        'image',
        'country',
        'password',
        'last_name',
        'first_name',
        'mobile_number',
        'email_verified_at',
    ];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    static $validationRules = [
        'register' => [
            'country' => 'required|string',
            'password' => 'required|confirmed',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'first_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:users'
        ]
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    protected $hidden = [
        'password',
        'remember_token',
        "email_verified_at",
        "created_at",
        "updated_at",
    ];

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function subscribes(): HasMany
    {
        return $this->hasMany(Subscribe::class);
    }
}
