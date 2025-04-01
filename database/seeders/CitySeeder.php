<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        City::truncate();
        Schema::enableForeignKeyConstraints();

        // Logolás letiltása
        activity()->disableLogging();

        $cities = [
            ['id' =>  1, 'region_id' => 1555, 'country_id' => 92, 'latitude' => '47.50000000', 'longitude' => '19.08333330', 'name' => 'Budapest',       'active' => 1],
            ['id' =>  2, 'region_id' => 1562, 'country_id' => 92, 'latitude' => '47.68333330', 'longitude' => '17.63333330', 'name' => 'Győr',           'active' => 1],
            ['id' =>  3, 'region_id' => 1545, 'country_id' => 92, 'latitude' => '47.68333330', 'longitude' => '17.78333330', 'name' => 'Kecskemét',      'active' => 1],
            ['id' =>  4, 'region_id' => 1548, 'country_id' => 92, 'latitude' => '46.90000000', 'longitude' => '19.78333330', 'name' => 'Kecskemét',      'active' => 1],
            ['id' =>  5, 'region_id' => 1546, 'country_id' => 92, 'latitude' => '47.16666670', 'longitude' => '19.80000000', 'name' => 'Cegléd',         'active' => 1],
            ['id' =>  6, 'region_id' => 1556, 'country_id' => 92, 'latitude' => '48.10000000', 'longitude' => '20.78333330', 'name' => 'Miskolc',        'active' => 1],
            ['id' =>  7, 'region_id' => 1561, 'country_id' => 92, 'latitude' => '46.08333330', 'longitude' => '18.23333330', 'name' => 'Pécs',           'active' => 1],
            ['id' =>  8, 'region_id' => 1545, 'country_id' => 92, 'latitude' => '47.68333330', 'longitude' => '16.60000000', 'name' => 'Sopron',         'active' => 1],
            ['id' =>  9, 'region_id' => 1539, 'country_id' => 92, 'latitude' => '47.20000000', 'longitude' => '18.41666670', 'name' => 'Székesfehérvár', 'active' => 1],
            ['id' => 10, 'region_id' => 1549, 'country_id' => 92, 'latitude' => '47.23333330', 'longitude' => '16.61666670', 'name' => 'Szombathely',    'active' => 1],
            ['id' => 11, 'region_id' => 1547, 'country_id' => 92, 'latitude' => '47.10000000', 'longitude' => '17.91666670', 'name' => 'Veszprém',       'active' => 1],
        ];

        $count = count($cities);

        $this->command->warn(PHP_EOL . __('migration_creating_cities'));
        $this->command->getOutput()->progressStart($count);

        foreach($cities as $city)
        {
            City::create($city);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . __('migration_created_cities'));

        activity()->enableLogging();
    }
}
