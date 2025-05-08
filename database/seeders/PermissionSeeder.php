<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Schema;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();
        
        // Logolás letiltása
        activity()->disableLogging();
        
        $permissions = [
            ['name' => 'read user'],
            ['name' => 'create user'],
            ['name' => 'update user'],
            ['name' => 'delete user'],
            
            ['name' => 'read company'],
            ['name' => 'create company'],
            ['name' => 'update company'],
            ['name' => 'delete company'],
            
            ['name' => 'read person'],
            ['name' => 'create person'],
            ['name' => 'update person'],
            ['name' => 'delete person'],
            
            ['name' => 'read entity'],
            ['name' => 'create entity'],
            ['name' => 'update entity'],
            ['name' => 'delete entity'],

            ['name' => 'read role'],
            ['name' => 'create role'],
            ['name' => 'update role'],
            ['name' => 'delete role'],

            ['name' => 'read permission'],
            ['name' => 'create permission'],
            ['name' => 'update permission'],
            ['name' => 'delete permission'],

            ['name' => 'read country'],
            ['name' => 'create country'],
            ['name' => 'update country'],
            ['name' => 'delete country'],

            ['name' => 'read region'],
            ['name' => 'create region'],
            ['name' => 'update region'],
            ['name' => 'delete region'],

            ['name' => 'read city'],
            ['name' => 'create city'],
            ['name' => 'update city'],
            ['name' => 'delete city'],
            
            ['name' => 'read subdomain'],
            ['name' => 'create subdomain'],
            ['name' => 'update subdomain'],
            ['name' => 'delete subdomain'],
            
            ['name' => 'read subdomain_state'],
            ['name' => 'create subdomain_state'],
            ['name' => 'update subdomain_state'],
            ['name' => 'delete subdomain_state'],

            ['name' => 'read calendar'],
            ['name' => 'create calendar'],
            ['name' => 'update calendar'],
            ['name' => 'delete calendar'],

            ['name' => 'read worktime_limit'],
            ['name' => 'create worktime_limit'],
            ['name' => 'update worktime_limit'],
            ['name' => 'delete worktime_limit'],
        ];
        
        $count = count($permissions);
        
        $this->command->warn(PHP_EOL . __('migration_creating_permissions'));
        $this->command->getOutput()->progressStart($count);
        
        foreach($permissions as $permission) {
            Permission::create($permission);
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . __('migration_created_permissions'));
        
        activity()->enableLogging();
        
    }
}
