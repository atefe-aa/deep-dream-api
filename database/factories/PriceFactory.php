<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lab_id'=> fake()->numberBetween(1,100),
            'test_type_id'=> fake()->numberBetween(1,30) ,
            'price'=> fake()->numberBetween(10000,500000) ,
            'price_per_slide'=> fake()->numberBetween(10000,50000) ,
            'description'=> fake()->sentence(10),
        ];
    }
}
