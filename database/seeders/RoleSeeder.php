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
            'name'          => 'superadmin'
        ]);

        // Jogosultságok társítása a Szuper Admin szerepkörhöz
        $superadmin->givePermissionTo([
            'delete user',
            'update user',
            'read user',
            'create user',

            'delete company',
            'update company',
            'read company',
            'create company',

            'delete role',
            'update role',
            'read role',
            'create role',

            'delete permission',
            'update permission',
            'read permission',
            'create permission',

            'delete entity',
            'update entity',
            'read entity',
            'create entity',
        ]);

        // Admin szerepkör létrehozása
        $admin = Role::create([
            'name'          => 'admin'
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
            'name'          => 'operator'
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
