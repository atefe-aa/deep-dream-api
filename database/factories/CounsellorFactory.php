<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class CounsellorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lab_id' => fake()->numberBetween(1, 10),
            'name' => fake()->name(),
            'description' => fake()->sentence(15),
            'phone' => fake()->unique()->phoneNumber(),
        ];
    }
}
