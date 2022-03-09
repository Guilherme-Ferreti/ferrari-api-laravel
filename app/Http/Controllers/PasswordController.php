<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'currentPassword'           => 'required|string|max:255|current_password:api',
            'newPassword'               => ['required', 'string', 'max:255', 'same:newPasswordConfirmation', PasswordRule::defaults()],
            'newPasswordConfirmation'   => 'required|string|max:255|same:newPassword',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->newPassword)
        ]);

        event(new PasswordReset(auth()->user()));

        return $this->respondOk([
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
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'bail|required|string',
            'email'                 => 'bail|required|string|email',
            'password'              => ['bail', 'required', 'string', PasswordRule::defaults()],
            'passwordConfirmation'  => ['bail', 'required', 'string', 'same:password'],
        ]);

        $status = Password::reset([
            'token'                 => $request->token,
            'email'                 => $request->email,
            'password'              => $request->password,
            'password_confirmation' => $request->passwordConfirmation,
        ], function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ?  response()->json(['status' => __($status)])
            :  response()->json(['email' => __($status)], 400);
    }
}
