<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_change_its_own_password()
    {
        Event::fake();

        $user = User::factory()->for(Person::factory())->create();

        $oldPassword = 'password';
        $newPassword = 'superSafePassword';

        $payload = [
            'currentPassword' => $oldPassword,
            'newPassword' => $newPassword,
            'newPasswordConfirmation' => $newPassword,
        ];

        $route = route('auth.change_password');

        $this->assertAuthenticatedOnly($route, 'put');

        $this->actingAs($user)->putJson($route, $payload)->assertOk();

        Event::assertDispatched(PasswordReset::class);

        $payload = [
            'email' => $user->email,
            'password' => $oldPassword,
        ];

        $this->postJson(route('auth.login'), $payload)->assertUnprocessable();

        $payload['password'] = $newPassword;

        $this->postJson(route('auth.login'), $payload)->assertOk();
    }
}
