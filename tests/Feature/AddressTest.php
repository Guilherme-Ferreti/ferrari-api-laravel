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
        $userA = User::factory()->for(Person::factory()->has(Address::factory(5)))->create();
        $userB = User::factory()->for(Person::factory()->has(Address::factory(3)))->create();

        $route = route('addresses.index');

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($userA)
            ->get($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(5)
                    ->first(fn (AssertableJson $json) =>
                        $json->hasAll('id', 'street', 'number', 'complement', 'district', 'city',
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
                $json->where('id', $address->id)
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

        $forbiddenUser = User::factory()->for(Person::factory())->create();
        $this->actingAs($forbiddenUser)->get($route)->assertForbidden();
    }

    public function test_an_address_can_be_created()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = Address::factory()->make()->toArray();

        $route = route('addresses.store');

        $this->assertAuthenticatedOnly($route);

        $this->actingAs($user)->postJson($route, $payload)->assertCreated();

        $payload['person_id'] = $user->person_id;

        $this->assertDatabaseHas(Address::class, $payload);
    }

    public function test_an_address_can_be_updated()
    {
        $user = User::factory()->for(Person::factory()->has(Address::factory()))->create();

        $payload = Address::factory()->make()->toArray();

        $route = route('addresses.update', $user->person->addresses[0]->id);

        $this->assertAuthenticatedOnly($route, 'put');

        $this->actingAs($user)->putJson($route, $payload)->assertOk();

        $payload['_id'] = $user->person->addresses[0]->id;
        $payload['person_id'] = $user->person_id;

        $this->assertDatabaseHas(Address::class, $payload);

        $forbiddenUser = User::factory()->for(Person::factory())->create();
        $this->actingAs($forbiddenUser)->putJson($route, $payload)->assertForbidden();
    }

    public function test_an_address_can_be_deleted()
    {
        $user = User::factory()->for(Person::factory()->has(Address::factory(2)))->create();

        $route = route('addresses.destroy', $user->person->addresses[0]->id);

        $this->assertAuthenticatedOnly($route, 'delete');

        $this->actingAs($user)->delete($route)->assertNoContent();

        $this->assertDatabaseMissing(Address::class, ['_id' => $user->person->addresses[0]->id]);

        $forbiddenUser = User::factory()->for(Person::factory())->create();
        $this->actingAs($forbiddenUser)
            ->delete(route('addresses.destroy', $user->person->addresses[1]->id))
            ->assertForbidden();
    }
}
