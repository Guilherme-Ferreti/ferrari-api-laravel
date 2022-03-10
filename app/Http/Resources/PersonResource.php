<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'birthAt'   => $this->birth_at,
            'phone'     => $this->phone,
            'document'  => $this->document,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            
            'user'      => new UserResource($this->whenLoaded('user')),
        ];
    }
}
