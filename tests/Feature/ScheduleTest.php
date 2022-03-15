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

class ScheduleTest extends TestCase
{
    use DatabaseMigrations;

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
            'service_ids'        => $services->pluck('id')->toArray(),
        ]);
    }
}
