<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_change_its_own_password()
    {
        $user = User::factory()->create();

        $oldPassword = 'password';
        $newPassword = 'superSafePassword';

        $payload = [
            'currentPassword' => $oldPassword,
            'newPassword' => $newPassword,
            'newPasswordConfirmation' => $newPassword,
        ];

        $this->actingAs($user, 'api')
            ->putJson(route('auth.change_password'), $payload)
            ->assertOk();

        $payload = [
            'email' => $user->email,
            'password' => $oldPassword,
        ];

        $this->postJson(route('auth.login'), $payload)->assertUnprocessable();

        $payload['password'] = $newPassword;

        $this->postJson(route('auth.login'), $payload)->assertOk();
    }
}
