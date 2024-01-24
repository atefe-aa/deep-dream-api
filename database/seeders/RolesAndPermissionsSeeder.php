<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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
            ['username' => "superadmin"],
            [
                'name' => "super admin",
                'phone' => "09100520741",
                'email' => "superadmin@voi.com",
                'password' => bcrypt("1-7EAYk6oc(v7P")
                ]
        );
        if ($superAdminUser)
            $superAdminUser->assignRole($superAdminRole);

        // Admin
        $operatorUser = User::firstOrCreate(
            ['username'=>"operator"],
            [
                'name'=>"operator",
                'phone'=>"09100520742",
                'email' => 'oprator@voi.com',
                'password' => bcrypt('password')
            ],
        );
        if ($operatorUser)
            $operatorUser->assignRole($operatorRole);

        //TourProvider
        $laboratoryUser = User::firstOrCreate(
            ['username'=>"milad"],
            [
                'name'=>"Milad",'phone'=>"09100520743",
                'email' => 'milad@voi.com',
                'password' => bcrypt('password')
            ],
        );
        if ($laboratoryUser)
            $laboratoryUser->assignRole($laboratoryRole);

    }
}
