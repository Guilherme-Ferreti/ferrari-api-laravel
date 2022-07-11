<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class TimeOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'day',
        'time',
    ];

    protected $casts = [
        'day' => 'integer',
    ];
}
