<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => fake()->numberBetween(1, 100),
            'lab_id' => fake()->numberBetween(1, 10),
            'sender_register_code' => fake()->numberBetween(100, 900),
            'test_type_id' => fake()->numberBetween(1, 10),
            'doctor_name' => fake()->name(),
            'price' => fake()->numberBetween(10000, 500000),
            'status' => fake()->randomElement(['registered', 'scanning', 'failed', 'answered', 'approved', 'suspended']),
            'num_slide' => fake()->numberBetween(1, 3),
            'description' => fake()->sentence(15),
        ];
    }
}
