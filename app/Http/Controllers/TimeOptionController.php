<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeOptionResource;
use App\Models\TimeOption;
use Illuminate\Http\Request;

class TimeOptionController extends Controller
{
    public function index()
    {
        return TimeOptionResource::collection(TimeOption::all());
    }

    public function store(Request $request)
    {
        $this->authorize('create', TimeOption::class);

        $attributes = $this->validate($request, [
            'day'  => 'required|integer|between:0,6',
            'time' => ['required', 'string', 'regex:/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/']
        ]);

        $timeOption = TimeOption::create($attributes);

        return $this->respondCreated(new TimeOptionResource($timeOption));
    }
}
