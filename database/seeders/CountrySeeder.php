<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class CountrySeeder extends Seeder
{
    /**
     * Auto generated seeder file.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Country::truncate();
        Schema::enableForeignKeyConstraints();

        // Logolás letiltása
        activity()->disableLogging();

        $countries = [
            ['id' => 11,'name' => 'Austria',            'code' => 'at','active' => 1],
            ['id' => 18,'name' => 'Belgium',               'code' => 'be','active' => 1],
            ['id' => 20,'name' => 'Bulgaria',              'code' => 'bg','active' => 1],
            ['id' => 33,'name' => 'Canada',           'code' => 'ca','active' => 1],
            ['id' => 58,'name' => 'Estonia','code' => 'ee','active' => 1],
            ['id' => 64,'name' => 'Finland','code' => 'fi','active' => 1],
            ['id' => 69,'name' => 'France','code' => 'fr','active' => 1],
            ['id' => 78,'name' => 'Greenland','code' => 'gl','active' => 1],
            ['id' => 92,'name' => 'Hungary','code' => 'hu','active' => 1],
            ['id' => 94,'name' => 'Ireland','code' => 'ie','active' => 1],
            ['id' => 99,'name' => 'Iceland','code' => 'is','active' => 1],
            ['id' => 100,'name' => 'Italy','code' => 'it','active' => 1],
            ['id' => 123,'name' => 'Luxembourg','code' => 'lu','active' => 1],
            ['id' => 124,'name' => 'Latvia','code' => 'lv','active' => 1],
            ['id' => 127,'name' => 'Monaco','code' => 'mc','active' => 1],
            ['id' => 128,'name' => 'Moldova','code' => 'md','active' => 1],
            ['id' => 131,'name' => 'Macedonia','code' => 'mk','active' => 1],
            ['id' => 150,'name' => 'Norfolk Island','code' => 'nf','active' => 1],
            ['id' => 154,'name' => 'Norway','code' => 'no','active' => 1],
            ['id' => 175,'name' => 'Romania','code' => 'ro','active' => 1],
            ['id' => 182,'name' => 'Sweden','code' => 'se','active' => 1],
        ];

        $count = count($countries);

        //\DB::table('countries')->insert($countries);

        $this->command->warn(PHP_EOL . __('migration_creating_countries'));
        $this->command->getOutput()->progressStart($count);

        foreach( $countries as $country )
        {
            Country::create($country);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . __('migration_created_countries'));

        activity()->enableLogging();
    }
}
