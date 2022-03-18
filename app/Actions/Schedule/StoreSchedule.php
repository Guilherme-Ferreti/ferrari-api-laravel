<?php

namespace App\Actions\Schedule;

use App\Models\Service;
use App\Models\Schedule;
use App\DTOs\StoreScheduleDTO;
use App\Models\PaymentSituation;

class StoreSchedule
{
    public function __invoke(StoreScheduleDTO $dto): Schedule
    {
        $attributes = $dto->toArray();

        $attributes['payment_situation_id'] = PaymentSituation::PAYMENT_PENDING;

        $attributes['total'] = Service::whereIn('_id', $attributes['services'])->sum('price');

        $attributes['person_id'] = auth()->user()->person_id;

        $schedule = Schedule::create($attributes);

        $schedule->services()->attach($attributes['services']);

        return $schedule;
    }
}
