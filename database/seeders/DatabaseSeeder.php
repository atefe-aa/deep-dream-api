<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Counsellor;
use App\Models\Laboratory;
use App\Models\LaboratoryMedia;
use App\Models\Patient;
use App\Models\Test;
use App\Models\TestType;
use App\Models\User;
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
        $this->call(RolesAndPermissionsSeeder::class);
        User::factory(10)->create();
        Laboratory::factory(10)->create();
        LaboratoryMedia::factory(10)->create();
        Patient::factory(100)->create();
        TestType::factory(10)->create();
        Test::factory(100)->create();
        Counsellor::factory(40)->create();

        $this->call(SettingSeeder::class);
        $this->call(PriceSeeder::class);
        $this->call(SlideSeeder::class);
    }
}
