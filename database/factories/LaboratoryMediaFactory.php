<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LaboratoryMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "lab_id" => fake()->unique()->numberBetween(1,100),
            "avatar" =>'https://i.pravatar.cc/48?u='.fake()->numberBetween(1,1000),
                "header" =>'https://i.pravatar.cc/100?u='.fake()->numberBetween(1,1000),
            "footer" =>'https://i.pravatar.cc/200?u='.fake()->numberBetween(1,1000),
            "signature" =>'https://i.pravatar.cc/48?u='.fake()->numberBetween(1,1000),
        ];
    }
}
