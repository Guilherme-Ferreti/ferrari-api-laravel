<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Person;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterRequest $request)
    {
        $attributes = $request->toDTO()->toArray();

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

        return $this->respondOk([
            'user' => new UserResource($user),
            'accessToken' => $token,
        ]);
    }
}
