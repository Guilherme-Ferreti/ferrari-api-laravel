<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'timeOptionId'     => $this->time_option_id,
            'billingAddressId' => $this->billing_address_id,
            'scheduleAt'       => $this->schedule_at,
            'installments'     => $this->installments,
            'total'            => $this->total,
            'services'         => $this->services->map(fn (Service $service) => [
                'id'    => $service->id,
                'name'  => $service->name,
            ]),
        ];
    }
}
