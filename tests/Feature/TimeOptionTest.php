<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\TimeOption;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TimeOptionTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_time_options_are_retrieved()
    {
        $timeOptions = TimeOption::factory(5)->create();

        $this->getJson(route('time_options.index'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(5)
                    ->first(fn (AssertableJson $json) =>
                        $json->where('id', $timeOptions[0]->id)
                            ->where('day', $timeOptions[0]->day)
                            ->where('time', $timeOptions[0]->time)
                            ->hasAll('createdAt', 'updatedAt')
                    )
            );
    }

    public function test_a_time_option_can_be_created()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();

        $payload = [
            'day'  => '1',
            'time' => '15:00',
        ];

        $route = route('time_options.store');

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->postJson($route, $payload)->assertCreated();

        $this->assertDatabaseHas(TimeOption::class, $payload);
    }

    public function test_date_and_time_combination_can_be_registered_only_once()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $timeOption = TimeOption::factory()->create();

        $payload = [
            'day'  => $timeOption->day,
            'time' => $timeOption->time,
        ];

        $this->actingAs($admin)
            ->postJson(route('time_options.store'), $payload)
            ->assertUnprocessable();
    }

    public function test_a_time_option_can_be_deleted()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $timeOption = TimeOption::factory()->create();

        $route = route('time_options.destroy', $timeOption);

        $this->assertAdminsOnly($route, 'delete');

        $this->actingAs($admin)->delete($route)->assertNoContent();

        $this->assertSoftDeleted(TimeOption::class, [
            '_id' => $timeOption->id,
        ]);
    }

    public function test_a_time_option_can_be_restored()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $timeOption = TimeOption::factory()->create();

        $timeOption->delete();

        $route = route('time_options.restore', $timeOption);

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->post($route)->assertOk();

        $this->assertNotSoftDeleted(TimeOption::class, [
            '_id' => $timeOption->id,
        ]);
    }
}
