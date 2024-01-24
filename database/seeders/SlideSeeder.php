<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i < 10; $i++)
        {
            $data[]=
                [
                    'nth'=>$i+1,
                    'bottom_left_x'=>$i*25,
                    'bottom_left_y'=>0,
                    'top_right_x'=>($i+1)*25,
                    'top_right_y'=>75,
                ];
        }

        DB::table('slides')->insert($data);
    }
}
