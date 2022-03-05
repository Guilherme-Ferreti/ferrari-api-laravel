<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Services\AuthService;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function test_jwt_authentication_works()
    {
        $user = User::factory()->for(Person::factory())->create();

        $jwt = AuthService::createJwtFor($user);

        $this->getJson(route('auth.profile.show'), ['Authorization' => "Bearer $jwt"])
            ->assertOk();
    }

    public function test_a_user_can_register()
    {
        $payload = [
            'email'     => 'user@gmail.com',
            'name'      => 'John Doe',
            'birthAt'   => '1990-01-01',
            'password'  => 'password',
            'phone'     => '5511912345678',
            'document'  => '123456789012',
        ];

        $this->postJson(route('auth.register'), $payload)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('accessToken')
                    ->has('user', fn (AssertableJson $json) => 
                        $json
                            ->hasAll('_id', 'photo', 'createdAt', 'updatedAt')
                            ->where('email', $payload['email'])
                            ->has('person', fn (AssertableJson $json) => 
                                $json
                                    ->hasAll('_id', 'createdAt', 'updatedAt')
                                    ->where('name', $payload['name'])
                                    ->where('birthAt', $payload['birthAt'])
                                    ->where('phone', $payload['phone'])
                                    ->where('document', $payload['document'])
                                    ->etc()
                            )
                            ->etc()
                    )
                );

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

    public function test_a_user_can_login()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = [
            'email'     => $user->email,
            'password'  => 'password',
        ];

        $this->postJson(route('auth.login'), $payload)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('accessToken')
                    ->has('user', fn (AssertableJson $json) => 
                        $json
                            ->hasAll('photo', 'createdAt', 'updatedAt')
                            ->where('_id', $user->id)
                            ->where('email', $user->email)
                            ->has('person', fn (AssertableJson $json) => 
                                $json
                                    ->where('_id', $user->person->id)
                                    ->where('name', $user->person->name)
                                    ->where('birthAt', $user->person->birth_at)
                                    ->where('phone', $user->person->phone)
                                    ->where('document', $user->person->document)
                                    ->hasAll('createdAt', 'updatedAt')
                                    ->etc()
                            )
                            ->etc()
                    )
                );
    }

    public function test_logged_in_user_information_can_be_retrieved()
    {
        $user = User::factory()->for(Person::factory())->create();
        
        $this->actingAs($user, 'api')
            ->getJson(route('auth.profile.show'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->hasAll('photo', 'createdAt', 'updatedAt')
                    ->where('_id', $user->id)
                    ->where('email', $user->email)
                    ->has('person', fn (AssertableJson $json) => 
                        $json
                            ->where('_id', $user->person->id)
                            ->where('name', $user->person->name)
                            ->where('birthAt', $user->person->birth_at)
                            ->where('phone', $user->person->phone)
                            ->where('document', $user->person->document)
                            ->hasAll('createdAt', 'updatedAt')
                            ->etc()
                    )
                    ->etc()
            );
    }
}
