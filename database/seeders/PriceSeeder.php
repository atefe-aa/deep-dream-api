<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\TestType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priceData = [];
        $labs = Laboratory::all()->pluck('id')->toArray();
        $testTypes = TestType::all()->pluck('id')->toArray();

        foreach ($labs as $lab) {
            foreach ($testTypes as $testType) {
                $priceData[] = [
                    'lab_id' => $lab,
                    'test_type_id' => $testType,
                    'price' => fake()->numberBetween(10000, 500000),
                    'price_per_slide' => fake()->numberBetween(10000, 50000),
                    'description' => fake()->sentence(10),
                ];
            }
        }

        DB::table('prices')->insert($priceData);
    }
}
