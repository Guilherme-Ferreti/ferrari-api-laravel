<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'country',
        'zipcode',
        'person_id',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
