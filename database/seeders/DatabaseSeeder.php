<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([

            CalendarSeeder::class,

            CountriesTableSeeder::class,
            RegionsTableSeeder::class,
            CitiesTableSeeder::class,
            //CountrySeeder::class,
            //RegionSeeder::class,
            //CitySeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,

            CompanySeeder::class,
            PersonSeeder::class,
            EntitySeeder::class,

            SubdomainStateSeeder::class,
            SubdomainSeeder::class,
        ]);
    }
}
