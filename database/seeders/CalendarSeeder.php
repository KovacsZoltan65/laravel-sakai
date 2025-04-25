<?php

namespace Database\Seeders;

use App\Traits\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Calendar;

class CalendarSeeder extends Seeder
{
    use DateTime;
    public function run(): void
    {
        $days = $this->getYearDays(Carbon::now()->year);

        foreach ($days as $day) {
            Calendar::create([
                'date' => $day,
            ]);
        }
    }
}