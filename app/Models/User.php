<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'photo',
        'person_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function isAdmin(): bool
    {
        return $this->email === 'guilherme@gmail.com';
    }
}
