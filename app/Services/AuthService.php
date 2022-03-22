<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials): array
    {
        $user = User::with('person')->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return [
            $user, 
            self::createJwtFor($user)
        ]; 
    }

    public static function createJwtFor(User $user): string
    {
        $payload = [
            'user' => [
                'id' => $user->id,
            ],
            'iat' => time(),
            'exp' => time() + config('jwt.expires_in'),
        ];

        return JWT::encode($payload, config('jwt.secret_key'), 'HS256');
    }
}
