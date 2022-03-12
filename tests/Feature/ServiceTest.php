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

    public function test_a_service_can_be_retrieved()
    {
        $service = Service::factory()->create();

        $this->getJson(route('services.show', $service))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->where('id', $service->id)
                    ->where('name', $service->name)
                    ->where('description', $service->description)
                    ->where('price', $service->price)
                    ->hasAll('createdAt', 'updatedAt')
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

    public function test_a_service_can_be_updated()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $service = Service::factory()->create();

        $payload = [
            'name'        => 'Ferrari Full Revision',
            'description' => 'A full revision for your Ferrari!',
            'price'       => 599.99,
        ];

        $route = route('services.update', $service);

        $this->assertAdminsOnly($route, 'put');

        $this->actingAs($admin)->putJson($route, $payload)->assertOk();

        $this->assertDatabaseHas(Service::class, [
            '_id' => $service->id,
            ...$payload,
        ]);
    }

    public function test_a_service_can_be_deleted()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $service = Service::factory()->create();

        $route = route('services.destroy', $service);

        $this->assertAdminsOnly($route, 'delete');

        $this->actingAs($admin)->delete($route)->assertNoContent();

        $this->assertSoftDeleted(Service::class, [
            '_id' => $service->id,
        ]);
    }

    public function test_a_service_can_be_restored()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $service = Service::factory()->create();

        $service->delete();

        $route = route('services.restore', $service);

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->post($route)->assertOk();

        $this->assertNotSoftDeleted(Service::class, [
            '_id' => $service->id,
        ]);
    }
}
