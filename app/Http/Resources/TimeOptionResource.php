<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'day'       => $this->day,
            'time'      => $this->time,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
