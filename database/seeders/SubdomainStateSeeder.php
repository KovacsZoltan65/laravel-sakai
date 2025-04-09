<?php

namespace Database\Seeders;

use App\Models\SubdomainState;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Nette\Schema\Schema as Schema2;

class SubdomainStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        SubdomainState::truncate();
        Schema::enableForeignKeyConstraints();

        activity()->disableLogging();

        $arr_subdomain_states = [
            ['id' => 1, 'name' => 'Nan', 'active' => 1],
            ['id' => 2, 'name' => 'Aktív', 'active' => 1],
            ['id' => 3, 'name' => 'Felfüggesztve', 'active' => 1],
            ['id' => 4, 'name' => 'Leállítva(HQ)', 'active' => 1],
            ['id' => 5, 'name' => 'Leállítva(példány)', 'active' => 1],
        ];

        $count = count($arr_subdomain_states);

        $this->command->warn(PHP_EOL . __('migration_creating_subdomain_states') );
        $this->command->getOutput()->progressStart($count);

        foreach($arr_subdomain_states as $state) {
            SubdomainState::factory()->create($state);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info(PHP_EOL . __('migration_created_subdomain_states') );

        activity()->enableLogging();
    }
}
