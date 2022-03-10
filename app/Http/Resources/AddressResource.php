<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'street'     => $this->street,
            'number'     => $this->number,
            'complement' => $this->complement,
            'district'   => $this->district,
            'city'       => $this->city,
            'state'      => $this->state,
            'country'    => $this->country,
            'zipcode'    => $this->zipcode,
            'personId'   => $this->person_id,
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,
        ];
    }
}
