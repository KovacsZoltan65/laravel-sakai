<?php

namespace Database\Seeders;

use App\Traits\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Calendar;
use Illuminate\Support\Facades\Schema;

class CalendarSeeder extends Seeder
{
    use DateTime;
    public function run(): void
    {
        // Külső kulcsok tiltása, tábla ürítése
        Schema::disableForeignKeyConstraints();
        Calendar::truncate();
        Schema::enableForeignKeyConstraints();

        // Logolás letiltása (ha Spatie Activitylog van használatban)
        activity()->disableLogging();

        $days = $this->getYearDays(Carbon::now()->year);

        $count = count($days);

        $this->command->warn("Creating {$count} days...");
        $this->command->getOutput()->progressStart($count);

        foreach ($days as $day) {
            Calendar::create([
                'date' => $day,
            ]);

            // Haladás jelzése
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("{$count} Calendar created.");

        activity()->enableLogging();
    }
}