<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Models\Service;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_services_are_retrieved()
    {
        Service::factory(5)->create();

        $this->get(route('services.index'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has(5)
                    ->first(fn (AssertableJson $json) =>
                        $json->hasAll('id', 'name', 'description', 'price', 'createdAt', 'updatedAt')
                    )
            );

    }

    public function test_a_service_can_be_created()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();

        $payload = [
            'name'        => 'Ferrari Full Revision',
            'description' => 'A full revision for your Ferrari!',
            'price'       => 599.99,
        ];

        $route = route('services.store');

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->postJson($route, $payload)->assertCreated();

        $this->assertDatabaseHas(Service::class, $payload);
    }
}
