<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeOption>
 */
class TimeOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'day'  => $this->faker->numberBetween(0, 6),
            'time' => $this->faker->time('H:i', '23:59'),
        ];
    }
}
