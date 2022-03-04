<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_edit_its_profile()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = [
            'email'     => 'new_email@gmail.com',
            'name'      => 'John Francis Doe',
            'birthAt'   => '1990-01-01',
            'phone'     => '5511985472265',
            'document'  => '81236821459',
        ];

        $response = $this->actingAs($user, 'api')->putJson(route('auth.profile.update'), $payload);

        $response->assertOk();

        $response->assertJson(fn (AssertableJson $json) => 
            $json
                ->where('_id', $user->id)
                ->where('email', $payload['email'])
                ->has('createdAt')
                ->has('updatedAt')
                ->has('person', fn (AssertableJson $json) => 
                    $json
                        ->where('_id', $user->person->id)
                        ->where('name', $payload['name'])
                        ->where('birthAt', $payload['birthAt'])
                        ->where('phone', $payload['phone'])
                        ->where('document', $payload['document'])
                        ->has('createdAt')
                        ->has('updatedAt')
                )
                ->etc()
        );
    }
}
