<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        auth()->user()->load('person');

        return new UserResource(auth()->user());
    }

    public function update(UpdateProfileRequest $request)
    {
        $attributes = $request->toDto()->toArray();

        auth()->user()->update($attributes);

        auth()->user()->person->update($attributes);

        return new UserResource(auth()->user());
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
        ]);

        if (auth()->user()->photo) {
            Storage::disk('public')->delete(auth()->user()->photo);
        }

        auth()->user()->update([
            'photo' => $request->file('photo')->store('photos', ['disk' => 'public']),
        ]);

        return new UserResource(auth()->user());
    }

    public function deletePhoto()
    {
        if (auth()->user()->photo) {
            Storage::disk('public')->delete(auth()->user()->photo);

            auth()->user()->update([
                'photo' => null,
            ]);
        }
        
        return new UserResource(auth()->user());
    }
}
