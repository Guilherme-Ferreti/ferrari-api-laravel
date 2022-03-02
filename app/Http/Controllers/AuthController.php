<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Person;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request)
    {
        $attributes = $request->validate([
            'email'     => 'bail|required|string|max:255|email|unique:users',
            'name'      => 'bail|required|string|max:255',
            'birth_at'  => 'bail|nullable|date_format:Y-m-d',
            'password'  => ['bail', 'required', 'string', Password::defaults()],
            'phone'     => 'bail|required|string|max:16|regex:/^\d+$/',
            'document'  => 'bail|required|string|max:16|regex:/^\d+$/',
        ]);

        $attributes['password'] = Hash::make($attributes['password']);

        $person = Person::create($attributes);

        $person->user()->create($attributes);

        return $this->login($request);
    }

    public function login(Request $request)
    {
        $crendentials = $request->validate([
            'email'     => 'bail|required|string|max:255|email',
            'password'  => ['bail', 'required', 'string', Password::defaults()],
        ]);

        [$user, $token] = $this->authService->login($crendentials);

        return response()->json([
            'user' => new UserResource($user),
            'accessToken' => $token,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        $user->load('person');

        return new UserResource($user);
    }
}
