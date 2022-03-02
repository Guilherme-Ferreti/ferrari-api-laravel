<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            '_id'       => $this->_id,
            'email'     => $this->email,
            'personId'  => $this->person_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            'personId'  => $this->person_id,
            'person'    => new PersonResource($this->whenLoaded('person')),
        ];
    }
}
