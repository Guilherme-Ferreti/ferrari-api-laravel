<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

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

    private function assertHasLoginResponse(TestResponse $response)
    {
        $response->assertJson(fn (AssertableJson $json) => 
            $json
                ->has('accessToken')
                ->has('user', fn (AssertableJson $json) => 
                    $json->has('person', fn (AssertableJson $json) => 
                            $json->hasAll(['_id', 'name', 'birth_at', 'phone', 'document', 'updated_at', 'created_at'])
                        )
                        ->hasAll([
                            '_id', 'email', 'person_id', 'created_at', 'updated_at', 'person',
                        ])
                )
        );
    }
}
