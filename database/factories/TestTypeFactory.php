<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TestTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numLayer = fake()->numberBetween(1,3);
        return [
            'title' => fake()->word(),
            'code' => fake()->numberBetween(100,900),
            'gender' => fake()->randomElement(['male','female','both']),
            'type' => fake()->randomElement(['optical','fluorescent','invert']),
            'num_layer' => $numLayer,
            'micro_step' => $numLayer > 1 ? fake()->numberBetween(1,100) : null,
            'step' => $numLayer > 1 ? fake()->numberBetween(1,100) : null,
            'z_axis' =>fake()->randomElement([fake()->numberBetween(1,100), null]),
            'condenser' => fake()->randomElement([fake()->numberBetween(1,100), null]),
            'brightness' => fake()->randomElement([fake()->numberBetween(1,100), null]),
            'magnification' =>fake()->randomElement([2,10,40,100]),
            'description' => fake()->sentence(10),
        ];
    }
}
