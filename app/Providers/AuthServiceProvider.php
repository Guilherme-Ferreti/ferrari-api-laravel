<?php

namespace App\Providers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->isProduction()
                ? $rule->mixedCase()->letters()->numbers()->symbols()
                : $rule;
        });

        Auth::viaRequest('jwt', function (Request $request) {
            try {
                $jwt = JWT::decode($request->bearerToken(), new Key(config('jwt.secret_key'), 'HS256'));

                if ($jwt->exp <= time()) {
                    return null;
                }

                return User::find($jwt->user->id);
            } catch (\Exception) {
                return null;
            }
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_app_url').'/reset-password?token='.$token;
        });
    }
}
