<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Shift;
use Illuminate\Support\Facades\Schema;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Shift::truncate();
        Schema::enableForeignKeyConstraints();

        // Logolás letiltása
        activity()->disableLogging();

        Company::factory()->count(3)->create()->each(function ($company) {
            $shifts = [
                [
                    'name' => 'Nappali műszak',
                    'code' => 'NAPPI',
                    'start_time' => '06:00:00',
                    'end_time' => '14:00:00',
                ],
                [
                    'name' => 'Délutáni műszak',
                    'code' => 'DELUT',
                    'start_time' => '14:00:00',
                    'end_time' => '22:00:00',
                ],
                [
                    'name' => 'Éjszakai műszak',
                    'code' => 'EJSZA',
                    'start_time' => '22:00:00',
                    'end_time' => '06:00:00',
                ],
            ];

            $count = count($shifts);

            $this->command->warn('Creating Shifts...');
            $this->command->getOutput()->progressStart($count);

            foreach ($shifts as $shift) {
                $start = Carbon::parse($shift['start_time']);
                $end = Carbon::parse($shift['end_time']);

                $crossesMidnight = $start->gt($end);
                $duration = $crossesMidnight
                    ? $start->diffInMinutes($end->copy()->addDay())
                    : $start->diffInMinutes($end);

                //$this->command->line("Cég: {$company->name} (ID: {$company->id})");

                Shift::create([
                    ...$shift,
                    'company_id' => $company->id,
                    'active' => true,
                    'duration_minutes' => $duration,
                    'crosses_midnight' => $crossesMidnight,
                ]);

                $this->command->getOutput()->progressAdvance();
            }
        });

        $this->command->getOutput()->progressFinish();

        $this->command->info('Created Entities');

        // Logolás engedélyezése
        activity()->enableLogging();

    }
}
