<?php

namespace App\Http\Controllers;

use App\DTO\RegisterDTO;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Person;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $registerDTO = $request->toDTO();

        $registerDTO->password = Hash::make($registerDTO->password);

        $person = Person::create($registerDTO->toArray());

        $person->user()->create($registerDTO->toArray());

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
