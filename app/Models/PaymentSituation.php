<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class PaymentSituation extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_PENDING = 1;

    const CANCELED = 2;

    const PAYMENT_APPROVED = 3;

    const PAYMENT_REVERSED = 4;

    const HANDLING = 5;

    const SENT = 6;

    protected $fillable = [
        '_id', 'name',
    ];
}
