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
    public function index()
    {
        $this->authorize('viewAny', Schedule::class);
        
        return ScheduleResource::collection(Schedule::latest()->paginate());
    }

    public function mySchedules()
    {
        $schedules = Schedule::where('person_id', auth()->user()->person_id)->latest()->paginate();

        return ScheduleResource::collection($schedules);
    }

    public function show(Schedule $schedule)
    {
        $this->authorize('view', $schedule);

        $schedule->load('timeOption', 'billingAddress', 'person', 'services');

        return new ScheduleResource($schedule);
    }

    public function store(StoreScheduleRequest $request)
    {
        $attributes = $request->toDTO()->toArray();

        $attributes['payment_situation_id'] = PaymentSituation::whereName('Payment Pending')->value('_id');

        $attributes['total'] = Service::whereIn('_id', $attributes['services'])->sum('price');

        $attributes['person_id'] = auth()->user()->person_id;

        $schedule = Schedule::create($attributes);

        $schedule->services()->attach($attributes['services']);

        $schedule->load('timeOption', 'billingAddress', 'person', 'services');

        return $this->respondCreated(new ScheduleResource($schedule));
    }

    public function markAsCompleted(Schedule $schedule)
    {
        $this->authorize('markAsCompleted', $schedule);

        $schedule->markAsCompleted();

        return new ScheduleResource($schedule);
    }
}
