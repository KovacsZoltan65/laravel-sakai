<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Region::truncate();
        Schema::enableForeignKeyConstraints();

        activity()->disableLogging();

        $regions = [
            ['id' => 1538,'name' => 'Komárom-Esztergom',     'code' => '12','country_id' => 92,'active' => 1,],
            ['id' => 1539,'name' => 'Fejér',                 'code' => '08','country_id' => 92,'active' => 1,],
            ['id' => 1540,'name' => 'Jász-Nagykun-Szolnok',  'code' => '20','country_id' => 92,'active' => 1,],
            ['id' => 1541,'name' => 'Baranya',               'code' => '02','country_id' => 92,'active' => 1,],
            ['id' => 1542,'name' => 'Szabolcs-Szatmár-Bereg','code' => '18','country_id' => 92,'active' => 1,],
            ['id' => 1543,'name' => 'Heves',                 'code' => '11','country_id' => 92,'active' => 1,],
            ['id' => 1544,'name' => 'Borsod-Abauj-Zemplén',  'code' => '04','country_id' => 92,'active' => 1,],
            ['id' => 1545,'name' => 'Győr-Moson-Sopron',     'code' => '09','country_id' => 92,'active' => 1,],
            ['id' => 1546,'name' => 'Pest',                  'code' => '16','country_id' => 92,'active' => 1,],
            ['id' => 1547,'name' => 'Veszprém',              'code' => '23','country_id' => 92,'active' => 1,],
            ['id' => 1548,'name' => 'Bács-Kiskun',           'code' => '01','country_id' => 92,'active' => 1,],
            ['id' => 1549,'name' => 'Vas',                   'code' => '22','country_id' => 92,'active' => 1,],
            ['id' => 1550,'name' => 'Hajdu-Bihar',           'code' => '10','country_id' => 92,'active' => 1,],
            ['id' => 1551,'name' => 'Zala',                  'code' => '24','country_id' => 92,'active' => 1,],
            ['id' => 1552,'name' => 'Somogy',                'code' => '17','country_id' => 92,'active' => 1,],
            ['id' => 1553,'name' => 'Tolna',                 'code' => '21','country_id' => 92,'active' => 1,],
            ['id' => 1554,'name' => 'Nográd',                'code' => '14','country_id' => 92,'active' => 1,],
            ['id' => 1555,'name' => 'Budapest',              'code' => '05','country_id' => 92,'active' => 1,],
            ['id' => 1556,'name' => 'Miskolc',               'code' => '13','country_id' => 92,'active' => 1,],
            ['id' => 1557,'name' => 'Csongrád',              'code' => '06','country_id' => 92,'active' => 1,],
            ['id' => 1558,'name' => 'Debrecen',              'code' => '07','country_id' => 92,'active' => 1,],
            ['id' => 1559,'name' => 'Békés',                 'code' => '03','country_id' => 92,'active' => 1,],
            ['id' => 1560,'name' => 'Szeged',                'code' => '19','country_id' => 92,'active' => 1,],
            ['id' => 1561,'name' => 'Pécs',                  'code' => '15','country_id' => 92,'active' => 1,],
            ['id' => 1562,'name' => 'Győr',                  'code' => '25','country_id' => 92,'active' => 1,],
        ];

        $count = count($regions);

        $this->command->warn(PHP_EOL . __('migration_creating_regions'));
        $this->command->getOutput()->progressStart($count);

        foreach($regions as $region)
        {
            Region::create($region);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . __('migration_created_regions'));

        activity()->enableLogging();

    }
}
