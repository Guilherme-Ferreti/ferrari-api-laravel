<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'scheduleAt'       => $this->schedule_at,
            'installments'     => $this->installments,
            'total'            => $this->total,
            'completedAt'      => $this->completed_at,
            'createdAt'        => $this->created_at,
            'updatedAt'        => $this->updated_at,

            'timeOptionId'     => $this->time_option_id,
            'billingAddressId' => $this->billing_address_id,
            'personId'         => $this->person_id,

            'timeOption'     => new TimeOptionResource($this->whenLoaded('timeOption')),
            'billingAddress' => new AddressResource($this->whenLoaded('billingAddress')),
            'person'         => new PersonResource($this->whenLoaded('person')),
            'services'       => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
