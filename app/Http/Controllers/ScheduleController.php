<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\PaymentSituation;
use App\Http\Resources\ScheduleResource;
use App\Http\Requests\StoreScheduleRequest;

class ScheduleController extends Controller
{
    public function store(StoreScheduleRequest $request)
    {
        $attributes = $request->toDTO()->toArray();

        $attributes['payment_situation_id'] = PaymentSituation::whereName('Payment Pending')->value('_id');

        $attributes['total'] = Service::whereIn('_id', $attributes['services'])->sum('price');

        $schedule = Schedule::create($attributes);

        $schedule->services()->attach($attributes['services']);

        return new ScheduleResource($schedule);
    }
}
