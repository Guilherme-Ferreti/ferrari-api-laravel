<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $user->load('person');

        return new UserResource($user);
    }

    public function update(UpdateProfileRequest $request)
    {
        $attributes = $request->toDto()->toArray();

        $request->user()->update($attributes);

        $request->user()->person->update($attributes);

        return new UserResource($request->user());
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
        ]);

        Storage::disk('public')->delete($request->user()->photo);

        $request->user()->update([
            'photo' => $request->file('photo')->store('photos', ['disk' => 'public']),
        ]);

        return new UserResource($request->user());
    }
}
