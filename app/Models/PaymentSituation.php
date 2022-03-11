<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class PaymentSituation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
