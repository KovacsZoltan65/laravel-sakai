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
            'delete user',
            'update user',

            'read entity',
            'create entity',
            'delete entity',
            'update entity',
            
            'read person',
            'create person',
            'delete person',
            'update person',
            
            'read company',
            'create company',
            'delete company',
            'update company',
            
            'read role',
            'create role',
            'delete role',
            'update role',

            'read permission',
            'create permission',
            'delete permission',
            'update permission',

            'read country',
            'create country',
            'delete country',
            'update country',

            'read region',
            'create region',
            'delete region',
            'update region',

            'read city',
            'create city',
            'delete city',
            'update city',
            
            'read subdomain',
            'create subdomain',
            'delete subdomain',
            'update subdomain',
            
            'read subdomain_state',
            'create subdomain_state',
            'delete subdomain_state',
            'update subdomain_state',

            'read calendar',
            'create calendar',
            'delete calendar',
            'update calendar',
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
