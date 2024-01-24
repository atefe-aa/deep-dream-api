<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male','female']);

        return [
            'name' => fake()->name($gender),
            'age' => fake()->numberBetween(1,100),
            'national_id' => fake()->unique()->numberBetween(1111111111,9999999999),
            'age_unit' => fake()->randomElement(['day','year']),
            'gender' => $gender
        ];
    }
}
