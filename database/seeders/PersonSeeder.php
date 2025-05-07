<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Külső kulcsok tiltása, tábla ürítése
        Schema::disableForeignKeyConstraints();
        Person::truncate();
        Schema::enableForeignKeyConstraints();

        activity()->disableLogging();

        $count = 20;

        $this->command->warn("Creating {$count} persons...");
        $this->command->getOutput()->progressStart($count);

        for ($i = 0; $i < $count; $i++) {
            $company = Person::factory()->create();

            // Haladás jelzése
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("{$count} persons created.");

        //$person = Person::factory(20)->create();

        activity()->enableLogging();
    }
}
