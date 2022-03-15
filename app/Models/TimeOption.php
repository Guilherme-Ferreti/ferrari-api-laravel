<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'day',
        'time',
    ];
}
