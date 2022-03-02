<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name(),
            'birth_at'  => $this->faker->date(),
            'phone'     => preg_replace('/[^0-9.]/', '', $this->faker->phoneNumber()),
            'document'  => $this->faker->cpf(false),
        ];
    }
}
