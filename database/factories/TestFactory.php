<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
            'patient_id' => fake()->numberBetween(1,100),
            'lab_id' => fake()->numberBetween(1,100),
            'sender_register_code' => fake()->numberBetween(100,900),
            'test_type_id' => fake()->numberBetween(1,30),
            'doctor_name' => fake()->name(),
            'price' => fake()->numberBetween(10000,500000),
            'status' => fake()->randomElement( ['registered','scanning','scanned','failed','answered','approved','suspended']),
            'num_slide' => fake()->numberBetween(1,3),
            'duration' => fake()->numberBetween(1,100),
            'description' => fake()->sentence(15),
        ];
    }
}
