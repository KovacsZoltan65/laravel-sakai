<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Szuper Admin szerepkör létrehozása
        $superadmin = Role::create([
            'name' => 'superadmin'
        ]);

        // Jogosultságok társítása a Szuper Admin szerepkörhöz
        $superadmin->givePermissionTo([
            'read user',
            'create user',
            'update user',
            'delete user',

            'read entity',
            'create entity',
            'update entity',
            'delete entity',
            
            'read person',
            'create person',
            'update person',
            'delete person',
            
            'read company',
            'create company',
            'update company',
            'delete company',
            
            'read role',
            'create role',
            'update role',
            'delete role',

            'read permission',
            'create permission',
            'update permission',
            'delete permission',

            'read country',
            'create country',
            'update country',
            'delete country',

            'read region',
            'create region',
            'update region',
            'delete region',

            'read city',
            'create city',
            'update city',
            'delete city',
            
            'read subdomain',
            'create subdomain',
            'update subdomain',
            'delete subdomain',
            
            'read subdomain_state',
            'create subdomain_state',
            'update subdomain_state',
            'delete subdomain_state',

            'read calendar',
            'create calendar',
            'update calendar',
            'delete calendar',

            'read worktime_limit',
            'create worktime_limit',
            'update worktime_limit',
            'delete worktime_limit',
        ]);

        // Admin szerepkör létrehozása
        $admin = Role::create([
            'name' => 'admin'
        ]);

        // Jogosultságok társítása az Admin szerepkörhöz
        $admin->givePermissionTo([
            'delete user',
            'update user',
            'read user',
            'create user',
            'read role',
            'read permission',
        ]);

        // Operátor szerepkör létrehozása
        $operator = Role::create([
            'name' => 'operator'
        ]);

        // Jogosultságok társítása az Operátor szerepkörhöz
        $operator->givePermissionTo([
            'read user',
            'create user',
            'read role',
            'read permission',
        ]);
    }
}
