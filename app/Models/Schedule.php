<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    const MAX_ALLOWED_INSTALLMENTS = 10;

    protected $fillable = [
        'schedule_at',
        'installments',
        'total',
        'time_option_id',
        'billing_address_id',
        'person_id',
    ];

    protected $casts = [
        'installments' => 'integer',
        'total' => 'float',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
