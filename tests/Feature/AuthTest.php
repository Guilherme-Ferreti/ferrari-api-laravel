<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Services\AuthService;
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
            'birthAt'  => '1990-01-01',
            'password'  => 'password',
            'phone'     => '5511912345678',
            'document'  => '123456789012',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertOk();

        $this->assertHasLoginResponse($response);

        $this->assertDatabaseHas(Person::class, [
            'name'      => $payload['name'],
            'birth_at'  => $payload['birthAt'],
            'phone'     => $payload['phone'],
            'document'  => $payload['document'],
        ]);

        $this->assertDatabaseHas(User::class, [
            'email' => $payload['email'],
        ]);
    }

    public function test_jwt_authentication_works()
    {
        $user = User::factory()->for(Person::factory())->create();

        $jwt = AuthService::createJwtFor($user);

        $this->getJson(route('auth.me'), ['Authorization' => "Bearer $jwt"])
            ->assertOk();
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

    public function test_logged_in_user_information_can_be_retrieved()
    {
        $user = User::factory()->for(Person::factory())->create();
        
        $this->actingAs($user, 'api')
            ->getJson(route('auth.me'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $this->assertHasUser($json)
            );
    }

    private function assertHasLoginResponse(TestResponse $response)
    {
        $response->assertJson(fn (AssertableJson $json) => 
            $json
                ->has('accessToken')
                ->has('user', fn (AssertableJson $json) => 
                    $this->assertHasUser($json)
                )
        );
    }

    private function assertHasUser(AssertableJson $json)
    {
        $json->hasAll('_id', 'email', 'createdAt', 'updatedAt', 'personId', 'person')
            ->has('person', fn (AssertableJson $json) => 
                $json->hasAll('_id', 'name', 'birthAt', 'phone', 'document', 'createdAt', 'updatedAt')
            );
    }
}
