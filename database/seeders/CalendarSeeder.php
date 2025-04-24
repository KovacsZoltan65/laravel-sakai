<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calendar;

class CalendarSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Calendar::factory()->count(20)->create();
    }
}