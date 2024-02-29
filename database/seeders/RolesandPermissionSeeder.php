<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesandPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name'=> 'Admin']);
        $role_standard = Role::create(['name'=> 'Standard']);


        $manage_issuances     =  Permission::create(['name' => 'manage_issuances']);
        $manage_user          =  Permission::create(['name' => 'manage_user']);
        $view_issuances       =  Permission::create(['name' =>'view_issuances']);

        $permission_admin =[$manage_issuances, $manage_user, $view_issuances];
        $permission_standard =[$view_issuances];

        $role_admin->syncPermissions($permission_admin);
        $role_standard->syncPermissions($permission_standard);

        User::find(1)->assignRole($role_admin);
        User::find(2)->assignRole($role_standard);
        User::find(3)->assignRole($role_admin);
    }
}
