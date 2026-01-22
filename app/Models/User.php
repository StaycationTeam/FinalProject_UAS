<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'username', 'is_admin'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function kingdom()
    {
        return $this->hasOne(Kingdom::class);
    }

    public function battlesAsAttacker()
    {
        return $this->hasManyThrough(Battle::class, Kingdom::class, 'user_id', 'attacker_id');
    }

    public function battlesAsDefender()
    {
        return $this->hasManyThrough(Battle::class, Kingdom::class, 'user_id', 'defender_id');
    }
}
