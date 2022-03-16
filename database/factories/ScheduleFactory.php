<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Person;
use App\Models\Schedule;
use App\Models\TimeOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'schedule_at'        => $this->faker->date(),
            'total'              => $this->faker->randomNumber(5) / 100,
            'installments'       => $this->faker->numberBetween(1, Schedule::MAX_ALLOWED_INSTALLMENTS),
            'time_option_id'     => TimeOption::factory(),
            'billing_address_id' => Address::factory()->for(Person::factory()->has(User::factory())),
            'person_id'          => fn (array $attributes) => Address::find($attributes['billing_address_id'])->person_id,
        ];
    }
}
