<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_register()
    {
        $payload = [
            'email'     => 'user@gmail.com',
            'name'      => 'John Doe',
            'birth_at'  => '1990-01-01',
            'password'  => 'password',
            'phone'     => '5511912345678',
            'document'  => '123456789012',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertOk();

        $this->assertHasLoginResponse($response);

        $this->assertDatabaseHas(Person::class, [
            'name'      => $payload['name'],
            'birth_at'  => $payload['birth_at'],
            'phone'     => $payload['phone'],
            'document'  => $payload['document'],
        ]);

        $this->assertDatabaseHas(User::class, [
            'email' => $payload['email'],
        ]);
    }

    public function test_a_user_can_login()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = [
            'email'     => $user->email,
            'password'  => 'password',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response->assertOk();

        $this->assertHasLoginResponse($response);
    }

    private function assertHasLoginResponse(TestResponse $response)
    {
        $response->assertJson(fn (AssertableJson $json) => 
            $json
                ->has('accessToken')
                ->has('user', fn (AssertableJson $json) => 
                    $json
                        ->hasAll([
                            '_id', 'email', 'createdAt', 'updatedAt', 'personId', 'person',
                        ])
                        ->has('person', fn (AssertableJson $json) => 
                            $json->hasAll(['_id', 'name', 'birthAt', 'phone', 'document', 'createdAt', 'updatedAt'])
                        )
                )
        );
    }
}
