<?php

namespace Database\Seeders;

use App\Models\EntityCalendar;
use Illuminate\Database\Seeder;

class EntityCalendarSeeder extends Seeder
{
    public function run(): void
    {
        EntityCalendar::factory()
            ->count(10) // 10 hónapnyi rekord, random entitásokhoz
            ->create();
    }
}
