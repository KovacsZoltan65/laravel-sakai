<?php

namespace Database\Seeders;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Traits\DateTime;

class CalendarSeeder extends Seeder
{
    use DateTime;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Calendar::truncate();
        Schema::enableForeignKeyConstraints();

        $year = now()->year;
        $dates = $this->getYearDays($year);
        //$now = Carbon::now();

        $records = array_map(fn($date) => ['date' => $date], $dates);

        activity()->disableLogging();

        $count = count($records);

        $this->command->warn(PHP_EOL . __('migration_creating_calendars'));
        $this->command->getOutput()->progressStart($count);

        foreach($records as $record) {
            Calendar::create([ 'date' => $record['date'], ]);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info(PHP_EOL . __('migration_created_calendars'));

        activity()->enableLogging();
    }
}
