<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'name', 'birth_at', 'phone', 'document',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
