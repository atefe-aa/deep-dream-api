<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    protected array $seeders = [
        RolesAndPermissionsSeeder::class,
        SettingSeeder::class,
        SlideSeeder::class,
    ];

    public function run(): void
    {
//        $this->call(RolesAndPermissionsSeeder::class);
//         User::factory(100)->create();
//         Laboratory::factory(100)->create();
//        LaboratoryMedia::factory(100)->create();
//         Patient::factory(100)->create();
//         TestType::factory(30)->create();
//         Price::factory(300)->create();
//         Test::factory(100)->create();
//         Counsellor::factory(100)->create();

//        $this->call(SettingSeeder::class);
//        $this->call(SlideSeeder::class);
    }
}
