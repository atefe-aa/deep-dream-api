<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');


        $superAdminRole = Role::create([
            'name' => 'superAdmin',
        ]);

        $operatorRole = Role::create([
            'name' => 'operator',
        ]);

        $laboratoryRole = Role::create([
            'name' => 'laboratory',
        ]);

        // Retrieve User by username, or create it with the username and password attributes ...
        $superAdminUser = User::firstOrCreate(
            ['username' => env('SUPER_ADMIN_USERNAME')],
            [
                'name' => env('SUPER_ADMIN_NAME'),
                'phone' => env('SUPER_ADMIN_PHONE'),
                'email' => env('SUPER_ADMIN_EMAIL'),
                'password' => env("SUPER_ADMIN_PASSWORD")
            ]
        );
        if ($superAdminUser)
            $superAdminUser->assignRole($superAdminRole);

        // OPERATOR
        $operatorUser = User::firstOrCreate(
            ['username' => env('OPERATOR_USERNAME')],
            [
                'name' => env('OPERATOR_NAME'),
                'phone' => env('OPERATOR_PHONE'),
                'email' => env('OPERATOR_EMAIL'),
                'password' => env("OPERATOR_PASSWORD")
            ],
        );
        if ($operatorUser)
            $operatorUser->assignRole($operatorRole);

        //LABORATORY
        $laboratoryUser = User::firstOrCreate(
            ['username' => env('LABORATORY_USERNAME')],
            [
                'name' => env('LABORATORY_NAME'),
                'phone' => env('LABORATORY_PHONE'),
                'email' => env('LABORATORY_EMAIL'),
                'password' => env("LABORATORY_PASSWORD")
            ],
        );
        if ($laboratoryUser)
            $laboratoryUser->assignRole($laboratoryRole);

    }
}
