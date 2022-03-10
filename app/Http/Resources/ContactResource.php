<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'message'    => $this->message,
            'personId'   => $this->person_id,
            'createdAt'  => $this->created_at,
            'updatedAt'  => $this->updated_at,

            'person'     => new PersonResource($this->whenLoaded('person')),
        ];
    }
}
