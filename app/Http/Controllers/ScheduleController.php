<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Actions\Schedule\StoreSchedule;
use App\Http\Resources\ScheduleResource;
use App\Http\Requests\StoreScheduleRequest;

class ScheduleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Schedule::class);

        return ScheduleResource::collection(Schedule::latest()->simplePaginate());
    }

    public function mySchedules()
    {
        $schedules = Schedule::where('person_id', auth()->user()->person_id)->latest()->simplePaginate();

        return ScheduleResource::collection($schedules);
    }

    public function show(Schedule $schedule)
    {
        $this->authorize('view', $schedule);

        return $this->scheduleResponse($schedule);
    }

    public function store(StoreScheduleRequest $request, StoreSchedule $storeSchedule)
    {
        $schedule = $storeSchedule($request->toDTO());

        return $this->respondCreated($this->scheduleResponse($schedule));
    }

    public function markAsCompleted(Schedule $schedule)
    {
        $this->authorize('markAsCompleted', $schedule);

        $schedule->markAsCompleted();

        return $this->scheduleResponse($schedule);
    }

    private function scheduleResponse($schedule): ScheduleResource
    {
        $schedule->load('timeOption', 'billingAddress', 'person', 'services');

        return new ScheduleResource($schedule);
    }
}
