<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Workplan;
use Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Faker\Factory;

class WorkplanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    Schema::disableForeignKeyConstraints();
    Workplan::truncate();
    Schema::enableForeignKeyConstraints();

    activity()->disableLogging();

    $company_ids = Company::pluck('id')->toArray();

    if (empty($company_ids)) {
        $this->command->error('Nincsenek cégek az adatbázisban.');
        return;
    }

    $count = 5;
    $this->command->warn('Creating Workplans...');
    $this->command->getOutput()->progressStart($count);

    $faker = Factory::create();
    $currentDate = $faker->dateTimeBetween('-1 year', '-6 months');

    for ($i = 1; $i <= $count; $i++) {
        $startDate = (clone $currentDate)->modify('+1 day');
        $endDate = (clone $startDate)->modify('+'.rand(3, 6).' months');

        Workplan::factory()->create([
            'name' => sprintf('Munkarend %02d', $i),
            'code' => strtoupper($faker->unique()->bothify('WP####')),
            'company_id' => Arr::random($company_ids),
            'daily_workhours' => 8,
            //'start_date' => $startDate->format('Y-m-d'),
            //'end_date' => $endDate->format('Y-m-d'),
        ]);

        $currentDate = $endDate;
        $this->command->getOutput()->progressAdvance();
    }

    $this->command->getOutput()->progressFinish();
    $this->command->info('Created Workplans');

    activity()->enableLogging();
}
}
