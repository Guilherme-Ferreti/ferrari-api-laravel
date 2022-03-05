<?php

namespace App\Http\Controllers;

use App\Events\PasswordUpdated;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'currentPassword'           => 'required|string|max:255|current_password:api',
            'newPassword'               => 'required|string|max:255|same:newPasswordConfirmation',
            'newPasswordConfirmation'   => 'required|string|max:255|same:newPassword',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->newPassword)
        ]);

        PasswordUpdated::dispatch($request->user());

        return response()->json([
            'message' => 'Password updated successfully!',
        ]);
    }
}
