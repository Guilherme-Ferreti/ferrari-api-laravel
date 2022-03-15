<?php

namespace App\Http\Controllers;

use App\Models\TimeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TimeOptionResource;

class TimeOptionController extends Controller
{
    public function index()
    {
        return TimeOptionResource::collection(TimeOption::all());
    }

    public function store(Request $request)
    {
        $this->authorize('create', TimeOption::class);

        $validator = Validator::make($request->all(), [
            'day'  => 'required|integer|between:0,6',
            'time' => ['required', 'string', 'regex:/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/']
        ])->stopOnFirstFailure();
        
        $validator->after(function ($validator) {
            $exists = TimeOption::where(request()->only('day', 'time'))->exists();

            if ($exists) {
                $validator->errors()->add('day', __('The day and time combination is already in use.'));
            }
        });

        $validator->validate();

        $timeOption = TimeOption::create($validator->validated());

        return $this->respondCreated(new TimeOptionResource($timeOption));
    }

    public function destroy(TimeOption $timeOption)
    {
        $this->authorize('delete', $timeOption);

        $timeOption->delete();

        return $this->respondNoContent();
    }

    public function restore(TimeOption $timeOption)
    {
        $this->authorize('restore', $timeOption);

        $timeOption->restore();

        return new TimeOptionResource($timeOption);
    }
}
