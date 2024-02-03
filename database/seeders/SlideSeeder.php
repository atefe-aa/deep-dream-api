<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $data[] =
                [
                    'nth' => $i + 1,
                    'sw_x' => $i * 25,
                    'sw_y' => 0,
                    'ne_x' => ($i + 1) * 25,
                    'ne_y' => 75,
                ];
        }

        DB::table('slides')->insert($data);
    }
}
