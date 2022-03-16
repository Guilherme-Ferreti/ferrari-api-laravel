<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Models\Address;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\TimeOption;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;

class ScheduleTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_schedules_can_be_retrieved()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        Schedule::factory(5)->has(Service::factory(3))->create();

        $route = route('schedules.index');

        $this->assertAdminsOnly($route, 'get');

        $this->actingAs($admin)
            ->getJson($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 5)
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json->hasAll(
                            'id', 'scheduleAt', 'installments', 'total', 'createdAt', 'updatedAt',
                            'timeOptionId', 'billingAddressId', 'personId',
                        )
                )
                ->etc()
            );
    }

    public function test_all_user_schedules_can_be_retrieved()
    {
        $user = User::factory()->for(Person::factory())->create();
        Schedule::factory(2)->for($user->person)->has(Service::factory(3))->create();
        
        Schedule::factory(5)->has(Service::factory(3))->create();

        $route = route('schedules.my_schedules');

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($user)
            ->getJson($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 2)
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json->hasAll(
                            'id', 'scheduleAt', 'installments', 'total', 'createdAt', 'updatedAt',
                            'timeOptionId', 'billingAddressId', 'personId',
                        )
                )
                ->etc()
            );
    }

    public function test_a_schedule_can_be_retrieved()
    {
        $user = User::factory()->for(Person::factory())->create();
        $schedule = Schedule::factory()->for($user->person)->has(Service::factory(3))->create();

        $route = route('schedules.show', $schedule->id);

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($user)
            ->get($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('id', $schedule->id)
                    ->where('scheduleAt', $schedule->schedule_at)
                    ->where('installments', $schedule->installments)
                    ->where('total', $schedule->total)
                    ->where('timeOptionId', $schedule->time_option_id)
                    ->where('billingAddressId', $schedule->billing_address_id)
                    ->where('personId', $schedule->person_id)
                    ->hasAll('createdAt', 'updatedAt', 'timeOption', 'billingAddress', 'person')
                    ->has('services', 3)
                    ->etc()
            );

        $forbiddenUser = User::factory()->for(Person::factory())->create();
        $this->actingAs($forbiddenUser)->get($route)->assertForbidden();
    }

    public function test_a_schedule_can_be_created()
    {
        $person = Person::factory()->has(User::factory())->has(Address::factory())->create();
        $timeOption = TimeOption::factory()->create(['day' => 1]);
        $services = Service::factory(3)->create();

        $payload = [
            'timeOptionId'     => $timeOption->id,
            'billingAddressId' => $person->addresses[0]->id,
            'scheduleAt'       => '2022-03-21',
            'installments'     => 5,
            'services'         => $services->pluck('id')->toArray(),
        ];

        $route = route('schedules.store');

        $this->assertAuthenticatedOnly($route);

        $this->actingAs($person->user)->postJson($route, $payload)->assertCreated();

        $this->assertDatabaseHas(Schedule::class, [
            'time_option_id'     => $payload['timeOptionId'],
            'billing_address_id' => $payload['billingAddressId'],
            'person_id'          => $person->id,
            'schedule_at'        => $payload['scheduleAt'],
            'installments'       => $payload['installments'],
            'total'              => $services->sum('price'),
        ]);

        $scheduleServices = Schedule::first()->services->pluck('id')->toArray();

        $services->each(fn (Service $service) => 
            $this->assertContains($service->id, $scheduleServices)
        );
    }
}
