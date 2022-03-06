<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use DatabaseMigrations;

    public function test_an_address_can_be_created()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = Address::factory()->make()->toArray();

        $route = route('addresses.store');

        $this->assertAuthenticatedOnly($route);

        $this->actingAs($user)
            ->postJson($route, $payload)
            ->assertCreated();

        $payload['person_id'] = $user->person_id;

        $this->assertDatabaseHas(Address::class, $payload);
    }
}
