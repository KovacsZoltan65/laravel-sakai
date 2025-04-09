<?php

namespace Database\Seeders;

use App\Models\Subdomain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SubdomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Subdomain::truncate();
        Schema::enableForeignKeyConstraints();

        activity()->disableLogging();

        $arr_subdomains = [
            ['id' => 1, 'subdomain' => 'showtime','url' => 'http://showtime.ejelenlet/', 'name' => 'Showtime - Ejelenlet Developer Instance', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_showtime', 'db_user' => 'ej3_showtime', 'db_password' => 'bqNxN86xwatT68wv', 'notification' => 1, 'state_id' => 1,],
            ['id' => 2, 'subdomain' => 'teszt','url' => 'http://teszt.ejelenlet/', 'name' => 'TESZT', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_test', 'db_user' => 'ej3_test', 'db_password' => 'bueQZTEQaXbeXGTr', 'notification' => 1, 'state_id' => 1,],
            ['id' => 3, 'subdomain' => 'da','url' => 'http://da.ejelenlet/', 'name' => 'Down Alapítvány', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_da_p', 'db_user' => 'ej3_da_p', 'db_password' => 'VQTTCmNHUB2uiKV', 'notification' => 1, 'state_id' => 1,],
            ['id' => 4, 'subdomain' => 'lgtoray','url' => 'http://lgtoray.ejelenlet/', 'name' => 'LG Toray Kft.', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_lgtoray', 'db_user' => 'ej3_lgtoray', 'db_password' => 'TCJpnkpTiO4H6F', 'notification' => 1, 'state_id' => 1,],
            ['id' => 5, 'subdomain' => 'mars','url' => 'http://mars.ejelenlet/', 'name' => 'mars_p', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_mars', 'db_user' => 'ej3_mars', 'db_password' => 'kxIrLsI9Z9ly3Qbx', 'notification' => 1, 'state_id' => 1,],
            ['id' => 6, 'subdomain' => 'llbghuber','url' => 'http://llbghuber.ejelenlet/', 'name' => 'Első Magyar Pékpont-rendszer Kft', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_llbghuber', 'db_user' => 'ej3_llbghuber', 'db_password' => 'r6nS3EkAx6TTkatQ', 'notification' => 1, 'state_id' => 1,],
            ['id' => 7, 'subdomain' => 'magyarepito','url' => 'http://magyarepito.ejelenlet/', 'name' => 'ej3_magyarepito_p', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_magyarepito_p', 'db_user' => 'ej3_magyarepito_p', 'db_password' => 'KU2tzHVWOAUL', 'notification' => 1, 'state_id' => 1,],
            ['id' => 8, 'subdomain' => 'masped','url' => 'http://masped.ejelenlet/', 'name' => 'ej3_masped_p', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_masped_p', 'db_user' => 'ej3_masped_p', 'db_password' => 'O4WsDVKDgncwDju', 'notification' => 1, 'state_id' => 1,],
            ['id' => 9, 'subdomain' => 'demo','url' => 'http://demo.ejelenlet/', 'name' => 'ej3_demo_p', 'db_host' => 'localhost', 'db_port' => 3306, 'db_name' => 'ej3_demo_p', 'db_user' => 'ej3_demo_p', 'db_password' => '7aiOSAOoVXwIoKhh', 'notification' => 1, 'state_id' => 1,]
        ];

        $count = count($arr_subdomains);

        $this->command->warn(PHP_EOL . __('migration_creating_subdomains') );

        $this->command->getOutput()->progressStart($count);

        foreach($arr_subdomains as $subdomain) {
            Subdomain::create($subdomain);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . __('migration_created_subdomains') );

        activity()->enableLogging();
    }
}
