<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

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

    protected $dates = [
        'completed_at',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function timeOption()
    {
        return $this->belongsTo(TimeOption::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function markAsCompleted(): void
    {
        $this->completed_at = now();

        $this->save();
    }

    public function isCompleted(): bool
    {
        return ! is_null($this->completed_at);
    }
}
