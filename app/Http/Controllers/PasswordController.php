<?php

namespace App\Http\Controllers;

use App\Events\PasswordUpdated;
use Illuminate\Auth\Events\PasswordReset;
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

        event(new PasswordReset($request->user()));

        return response()->json([
            'message' => 'Password updated successfully!',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)], 400);
    }
}
