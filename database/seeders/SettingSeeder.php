<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        DB::table('settings_categories')->insert([
//            [
//                'title' => '2x',
//            ],
//            [
//                'title' => '10x',
//            ],
//            [
//                'title' => '40x',
//            ],
//            [
//                'title' => '100x',
//            ],
//            [
//                'title' => 'condenser',
//            ],
//        ]);

        $settings = [
            [
                'category_id' => 1,
                'key' => 'placement',
                'value' => 1,
                'default' => 2,
                'unit' => 'degree'
            ],
            [
                'category_id' => 1,
                'key' => 'condenser',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'brightness',
                'value' => 10,
                'default' => 20,
                'unit' => '%'
            ],
            [
                'category_id' => 1,
                'key' => 'micro-step',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'x',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'y',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'z',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'min-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 1,
                'key' => 'max-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ], [
                'category_id' => 1,
                'key' => 'number-of-layers',
                'value' => 13,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 1,
                'key' => 'number-of-merge-layers',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 1,
                'key' => 'merge-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 1,
                'key' => 'stitch-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ],
            [
                'category_id' => 2,
                'key' => 'placement',
                'value' => 20,
                'default' => 2,
                'unit' => 'degree'
            ],
            [
                'category_id' => 2,
                'key' => 'condenser',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'brightness',
                'value' => 10,
                'default' => 20,
                'unit' => '%'
            ],
            [
                'category_id' => 2,
                'key' => 'micro-step',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'x',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'y',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'z',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'min-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 2,
                'key' => 'max-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ], [
                'category_id' => 2,
                'key' => 'number-of-layers',
                'value' => 13,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 2,
                'key' => 'number-of-merge-layers',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 2,
                'key' => 'merge-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 2,
                'key' => 'stitch-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ],
            [
                'category_id' => 3,
                'key' => 'placement',
                'value' => 45,
                'default' => 2,
                'unit' => 'degree'
            ],
            [
                'category_id' => 3,
                'key' => 'condenser',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'brightness',
                'value' => 10,
                'default' => 20,
                'unit' => '%'
            ],
            [
                'category_id' => 3,
                'key' => 'micro-step',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'x',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'y',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'z',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'min-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 3,
                'key' => 'max-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ], [
                'category_id' => 3,
                'key' => 'number-of-layers',
                'value' => 13,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 3,
                'key' => 'number-of-merge-layers',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 3,
                'key' => 'merge-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 3,
                'key' => 'stitch-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ],
            [
                'category_id' => 4,
                'key' => 'placement',
                'value' => 80,
                'default' => 2,
                'unit' => 'degree'
            ],
            [
                'category_id' => 4,
                'key' => 'condenser',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'brightness',
                'value' => 10,
                'default' => 20,
                'unit' => '%'
            ],
            [
                'category_id' => 4,
                'key' => 'micro-step',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'x',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'y',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'z',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'min-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'max-focus',
                'value' => 1,
                'default' => 2,
                'unit' => 'mm'
            ],
            [
                'category_id' => 4,
                'key' => 'number-of-layers',
                'value' => 13,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 4,
                'key' => 'number-of-merge-layers',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 4,
                'key' => 'merge-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ], [
                'category_id' => 4,
                'key' => 'stitch-algorithm',
                'value' => 5,
                'default' => 2,
                'unit' => ''
            ],
            [
                'category_id' => 5,
                'key' => 'min',
                'value' => 10,
                'default' => 20,
                'unit' => 'mm'
            ],
            [
                'category_id' => 5,
                'key' => 'max',
                'value' => 10,
                'default' => 20,
                'unit' => 'mm'
            ],
        ];

        DB::table('settings')->insert($settings);
    }
}
