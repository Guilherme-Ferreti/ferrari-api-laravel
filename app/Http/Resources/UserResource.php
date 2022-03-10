<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'photo'     => $this->photo ? asset('storage/' . $this->photo) : null,
            'personId'  => $this->person_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

            'personId' => $this->person_id,
            'person'   => new PersonResource($this->whenLoaded('person')),
        ];
    }
}
