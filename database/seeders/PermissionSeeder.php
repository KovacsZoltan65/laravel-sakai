<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'read user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'update user']);

        Permission::create(['name' => 'read company']);
        Permission::create(['name' => 'create company']);
        Permission::create(['name' => 'delete company']);
        Permission::create(['name' => 'update company']);

        Permission::create(['name' => 'read entity']);
        Permission::create(['name' => 'create entity']);
        Permission::create(['name' => 'delete entity']);
        Permission::create(['name' => 'update entity']);

        Permission::create(['name' => 'read role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'delete role']);
        Permission::create(['name' => 'update role']);

        Permission::create(['name' => 'read permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'delete permission']);
        Permission::create(['name' => 'update permission']);

        Permission::create(['name' => 'read country']);
        Permission::create(['name' => 'create country']);
        Permission::create(['name' => 'delete country']);
        Permission::create(['name' => 'update country']);

        Permission::create(['name' => 'read region']);
        Permission::create(['name' => 'create region']);
        Permission::create(['name' => 'delete region']);
        Permission::create(['name' => 'update region']);

        Permission::create(['name' => 'read city']);
        Permission::create(['name' => 'create city']);
        Permission::create(['name' => 'delete city']);
        Permission::create(['name' => 'update city']);
    }
}
