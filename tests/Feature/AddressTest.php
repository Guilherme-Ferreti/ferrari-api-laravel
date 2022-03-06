<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_users_addresses_are_returned()
    {
        $users = User::factory(2)
            ->for(Person::factory()->has(Address::factory(5)))
            ->create();

        $route = route('addresses.index');

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($users[0])
            ->get($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has(5)
                    ->first(fn (AssertableJson $json) => 
                        $json->hasAll('_id', 'street', 'number', 'complement', 'district', 'city', 
                            'state', 'country', 'zipcode', 'personId', 'createdAt', 'updatedAt'
                        )
                    )
            );
    }

    public function test_an_address_can_be_retrieved()
    {
        $person = Person::factory()->has(User::factory())->has(Address::factory())->create();

        $address = $person->addresses[0];

        $route = route('addresses.show', $address->id);

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($person->user)
            ->get($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('_id', $address->id)
                    ->where('street', $address->street)
                    ->where('number', $address->number)
                    ->where('complement', $address->complement)
                    ->where('district', $address->district)
                    ->where('city', $address->city)
                    ->where('state', $address->state)
                    ->where('country', $address->country)
                    ->where('zipcode', $address->zipcode)
                    ->where('personId', $address->person_id)
                    ->hasAll('createdAt', 'updatedAt')
                    ->etc()
            );

        $person = Person::factory()->has(User::factory())->create();

        $this->actingAs($person->user)->get($route)->assertNotFound();
    }

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
