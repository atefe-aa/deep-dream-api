<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
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
            "lab_id" => fake()->numberBetween(1, 10),
            "avatar" => 'https://i.pravatar.cc/48?u=' . fake()->numberBetween(1, 100),
            "header" => 'https://i.pravatar.cc/100?u=' . fake()->numberBetween(1, 100),
            "footer" => 'https://i.pravatar.cc/200?u=' . fake()->numberBetween(1, 100),
            "signature" => 'https://i.pravatar.cc/48?u=' . fake()->numberBetween(1, 100),
        ];
    }
}
