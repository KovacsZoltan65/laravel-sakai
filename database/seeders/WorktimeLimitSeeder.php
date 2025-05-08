<?php

namespace Database\Seeders;

use App\Models\WorktimeLimit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class WorktimeLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        WorktimeLimit::truncate();
        Schema::enableForeignKeyConstraints();

        // Logolás letiltása
        activity()->disableLogging();

        $count = 10;

        $this->command->getOutput()->progressStart($count);

        for( $i = 1; $i <= $count; $i++ ) {
            WorktimeLimit::factory()->create();
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Created WorktimeLimits');
        // Logolás engedélyezése
        activity()->enableLogging();
    }
}
