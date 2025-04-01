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
            /*
            ['id' => 1,'name' => 'Andorra',             'code' => 'ad','active' => 0],
            ['id' => 2,'name' => 'United Arab Emirates','code' => 'ae','active' => 0],
            ['id' => 3,'name' => 'Afghanistan',         'code' => 'af','active' => 0],
            ['id' => 4,'name' => 'Antigua and Barbuda', 'code' => 'ag','active' => 0],
            ['id' => 5,'name' => 'Anguilla',            'code' => 'ai','active' => 0],
            ['id' => 6,'name' => 'Albania',             'code' => 'al','active' => 0],
            ['id' => 7,'name' => 'Armenia',             'code' => 'am','active' => 0],
            ['id' => 8,'name' => 'Netherlands Antilles','code' => 'an','active' => 0],
            ['id' => 9,'name' => 'Angola',              'code' => 'ao','active' => 0],
            ['id' => 10,'name' => 'Argentina',          'code' => 'ar','active' => 0],
            */
            ['id' => 11,'name' => 'Austria',            'code' => 'at','active' => 1],
            /*
            ['id' => 12,'name' => 'Australia',             'code' => 'au','active' => 0],
            ['id' => 13,'name' => 'Aruba',                 'code' => 'aw','active' => 0],
            ['id' => 14,'name' => 'Azerbaijan',            'code' => 'az','active' => 0],
            ['id' => 15,'name' => 'Bosnia and Herzegovina','code' => 'ba','active' => 0],
            ['id' => 16,'name' => 'Barbados',              'code' => 'bb','active' => 0],
            ['id' => 17,'name' => 'Bangladesh',            'code' => 'bd','active' => 0],
            */
            ['id' => 18,'name' => 'Belgium',               'code' => 'be','active' => 1],
            //['id' => 19,'name' => 'Burkina Faso',          'code' => 'bf','active' => 0],
            ['id' => 20,'name' => 'Bulgaria',              'code' => 'bg','active' => 1],
            /*
            ['id' => 21,'name' => 'Bahrain',          'code' => 'bh','active' => 0],
            ['id' => 22,'name' => 'Burundi',          'code' => 'bi','active' => 0],
            ['id' => 23,'name' => 'Benin',            'code' => 'bj','active' => 0],
            ['id' => 24,'name' => 'Bermuda',          'code' => 'bm','active' => 0],
            ['id' => 25,'name' => 'Brunei Darussalam','code' => 'bn','active' => 0],
            ['id' => 26,'name' => 'Bolivia',          'code' => 'bo','active' => 0],
            ['id' => 27,'name' => 'Brazil',           'code' => 'br','active' => 0],
            ['id' => 28,'name' => 'Bahamas',          'code' => 'bs','active' => 0],
            ['id' => 29,'name' => 'Bhutan',           'code' => 'bt','active' => 0],
            ['id' => 30,'name' => 'Botswana',         'code' => 'bw','active' => 0],
            ['id' => 31,'name' => 'Belarus',          'code' => 'by','active' => 0],
            ['id' => 32,'name' => 'Belize',           'code' => 'bz','active' => 0],
            */
            ['id' => 33,'name' => 'Canada',           'code' => 'ca','active' => 1],
            /*
            ['id' => 34,'name' => 'Cocos (Keeling) Islands','code' => 'cc','active' => 0],
            ['id' => 35,'name' => 'Democratic Republic of the Congo','code' => 'cd','active' => 0],
            ['id' => 36,'name' => 'Central African Republic','code' => 'cf','active' => 0],
            ['id' => 37,'name' => 'Congo','code' => 'cg','active' => 0],
            ['id' => 38,'name' => 'Switzerland','code' => 'ch','active' => 0],
            ['id' => 39,'name' => 'Cote D\'Ivoire (Ivory Coast)','code' => 'ci','active' => 0],
            ['id' => 40,'name' => 'Cook Islands','code' => 'ck','active' => 0],
            ['id' => 41,'name' => 'Chile','code' => 'cl','active' => 0],
            ['id' => 42,'name' => 'Cameroon','code' => 'cm','active' => 0],
            ['id' => 43,'name' => 'China','code' => 'cn','active' => 0],
            ['id' => 44,'name' => 'Colombia','code' => 'co','active' => 0],
            ['id' => 45,'name' => 'Costa Rica','code' => 'cr','active' => 0],
            ['id' => 46,'name' => 'Cuba','code' => 'cu','active' => 0],
            ['id' => 47,'name' => 'Cape Verde','code' => 'cv','active' => 0],
            ['id' => 48,'name' => 'Christmas Island','code' => 'cx','active' => 0],
            ['id' => 49,'name' => 'Cyprus','code' => 'cy','active' => 0],
            ['id' => 50,'name' => 'Czech Republic','code' => 'cz','active' => 0],
            ['id' => 51,'name' => 'Germany','code' => 'de','active' => 0],
            ['id' => 52,'name' => 'Djibouti','code' => 'dj','active' => 0],
            ['id' => 53,'name' => 'Denmark','code' => 'dk','active' => 0],
            ['id' => 54,'name' => 'Dominica','code' => 'dm','active' => 0],
            ['id' => 55,'name' => 'Dominican Republic','code' => 'do','active' => 0],
            ['id' => 56,'name' => 'Algeria','code' => 'dz','active' => 0],
            ['id' => 57,'name' => 'Ecuador','code' => 'ec','active' => 0],
            */
            ['id' => 58,'name' => 'Estonia','code' => 'ee','active' => 1],
            /*
            ['id' => 59,'name' => 'Egypt','code' => 'eg','active' => 0],
            ['id' => 60,'name' => 'Western Sahara','code' => 'eh','active' => 0],
            ['id' => 61,'name' => 'Eritrea','code' => 'er','active' => 0],
            ['id' => 62,'name' => 'Spain','code' => 'es','active' => 0],
            ['id' => 63,'name' => 'Ethiopia','code' => 'et','active' => 0],
            */
            ['id' => 64,'name' => 'Finland','code' => 'fi','active' => 1],
            /*
            ['id' => 65,'name' => 'Fiji','code' => 'fj','active' => 0],
            ['id' => 66,'name' => 'Falkland Islands (Malvinas)','code' => 'fk','active' => 0],
            ['id' => 67,'name' => 'Federated States of Micronesia','code' => 'fm','active' => 0],
            ['id' => 68,'name' => 'Faroe Islands','code' => 'fo','active' => 0],
            */
            ['id' => 69,'name' => 'France','code' => 'fr','active' => 1],
            /*
            ['id' => 70,'name' => 'Gabon','code' => 'ga','active' => 0],
            ['id' => 71,'name' => 'Great Britain (UK)','code' => 'gb','active' => 0],
            ['id' => 72,'name' => 'Grenada','code' => 'gd','active' => 0],
            ['id' => 73,'name' => 'Georgia','code' => 'ge','active' => 0],
            ['id' => 74,'name' => 'French Guiana','code' => 'gf','active' => 0],
            ['id' => 75,'name' => 'NULL','code' => 'gg','active' => 0],
            ['id' => 76,'name' => 'Ghana','code' => 'gh','active' => 0],
            ['id' => 77,'name' => 'Gibraltar','code' => 'gi','active' => 0],
            */
            ['id' => 78,'name' => 'Greenland','code' => 'gl','active' => 1],
            /*
            ['id' => 79,'name' => 'Gambia','code' => 'gm','active' => 0],
            ['id' => 80,'name' => 'Guinea','code' => 'gn','active' => 0],
            ['id' => 81,'name' => 'Guadeloupe','code' => 'gp','active' => 0],
            ['id' => 82,'name' => 'Equatorial Guinea','code' => 'gq','active' => 0],
            ['id' => 83,'name' => 'Greece','code' => 'gr','active' => 0],
            ['id' => 84,'name' => 'S. Georgia and S. Sandwich Islands','code' => 'gs','active' => 0],
            ['id' => 85,'name' => 'Guatemala','code' => 'gt','active' => 0],
            ['id' => 86,'name' => 'Guinea-Bissau','code' => 'gw','active' => 0],
            ['id' => 87,'name' => 'Guyana','code' => 'gy','active' => 0],
            ['id' => 88,'name' => 'Hong Kong','code' => 'hk','active' => 0],
            ['id' => 89,'name' => 'Honduras','code' => 'hn','active' => 0],
            ['id' => 90,'name' => 'Croatia (Hrvatska)','code' => 'hr','active' => 0],
            ['id' => 91,'name' => 'Haiti','code' => 'ht','active' => 0],
            */
            ['id' => 92,'name' => 'Hungary','code' => 'hu','active' => 1],
            //['id' => 93,'name' => 'Indonesia','code' => 'id','active' => 0],
            ['id' => 94,'name' => 'Ireland','code' => 'ie','active' => 1],
            /*
            ['id' => 95,'name' => 'Israel','code' => 'il','active' => 0],
            ['id' => 96,'name' => 'India','code' => 'in','active' => 0],
            ['id' => 97,'name' => 'Iraq','code' => 'iq','active' => 0],
            ['id' => 98,'name' => 'Iran','code' => 'ir','active' => 0],
            */
            ['id' => 99,'name' => 'Iceland','code' => 'is','active' => 1],
            ['id' => 100,'name' => 'Italy','code' => 'it','active' => 1],
            /*
            ['id' => 101,'name' => 'Jamaica','code' => 'jm','active' => 0],
            ['id' => 102,'name' => 'Jordan','code' => 'jo','active' => 0],
            ['id' => 103,'name' => 'Japan','code' => 'jp','active' => 0],
            ['id' => 104,'name' => 'Kenya','code' => 'ke','active' => 0],
            ['id' => 105,'name' => 'Kyrgyzstan','code' => 'kg','active' => 0],
            ['id' => 106,'name' => 'Cambodia','code' => 'kh','active' => 0],
            ['id' => 107,'name' => 'Kiribati','code' => 'ki','active' => 0],
            ['id' => 108,'name' => 'Comoros','code' => 'km','active' => 0],
            ['id' => 109,'name' => 'Saint Kitts and Nevis','code' => 'kn','active' => 0],
            ['id' => 110,'name' => 'Korea (North)','code' => 'kp','active' => 0],
            ['id' => 111,'name' => 'Korea (South)','code' => 'kr','active' => 0],
            ['id' => 112,'name' => 'Kuwait','code' => 'kw','active' => 0],
            ['id' => 113,'name' => 'Cayman Islands','code' => 'ky','active' => 0],
            ['id' => 114,'name' => 'Kazakhstan','code' => 'kz','active' => 0],
            ['id' => 115,'name' => 'Laos','code' => 'la','active' => 0],
            ['id' => 116,'name' => 'Lebanon','code' => 'lb','active' => 0],
            ['id' => 117,'name' => 'Saint Lucia','code' => 'lc','active' => 0],
            ['id' => 118,'name' => 'Liechtenstein','code' => 'li','active' => 0],
            ['id' => 119,'name' => 'Sri Lanka','code' => 'lk','active' => 0],
            ['id' => 120,'name' => 'Liberia','code' => 'lr','active' => 0],
            ['id' => 121,'name' => 'Lesotho','code' => 'ls','active' => 0],
            ['id' => 122,'name' => 'Lithuania','code' => 'lt','active' => 0],
            */
            ['id' => 123,'name' => 'Luxembourg','code' => 'lu','active' => 1],
            ['id' => 124,'name' => 'Latvia','code' => 'lv','active' => 1],
            //['id' => 125,'name' => 'Libya','code' => 'ly','active' => 0],
            //['id' => 126,'name' => 'Morocco','code' => 'ma','active' => 0],
            ['id' => 127,'name' => 'Monaco','code' => 'mc','active' => 1],
            ['id' => 128,'name' => 'Moldova','code' => 'md','active' => 1],
            //['id' => 129,'name' => 'Madagascar','code' => 'mg','active' => 0],
            //['id' => 130,'name' => 'Marshall Islands','code' => 'mh','active' => 0],
            ['id' => 131,'name' => 'Macedonia','code' => 'mk','active' => 1],
            /*
            ['id' => 132,'name' => 'Mali','code' => 'ml','active' => 0],
            ['id' => 133,'name' => 'Myanmar','code' => 'mm','active' => 0],
            ['id' => 134,'name' => 'Mongolia','code' => 'mn','active' => 0],
            ['id' => 135,'name' => 'Macao','code' => 'mo','active' => 0],
            ['id' => 136,'name' => 'Northern Mariana Islands','code' => 'mp','active' => 0],
            ['id' => 137,'name' => 'Martinique','code' => 'mq','active' => 0],
            ['id' => 138,'name' => 'Mauritania','code' => 'mr','active' => 0],
            ['id' => 139,'name' => 'Montserrat','code' => 'ms','active' => 0],
            ['id' => 140,'name' => 'Malta','code' => 'mt','active' => 0],
            ['id' => 141,'name' => 'Mauritius','code' => 'mu','active' => 0],
            ['id' => 142,'name' => 'Maldives','code' => 'mv','active' => 0],
            ['id' => 143,'name' => 'Malawi','code' => 'mw','active' => 0],
            ['id' => 144,'name' => 'Mexico','code' => 'mx','active' => 0],
            ['id' => 145,'name' => 'Malaysia','code' => 'my','active' => 0],
            ['id' => 146,'name' => 'Mozambique','code' => 'mz','active' => 0],
            ['id' => 147,'name' => 'Namibia','code' => 'na','active' => 0],
            ['id' => 148,'name' => 'New Caledonia','code' => 'nc','active' => 0],
            ['id' => 149,'name' => 'Niger','code' => 'ne','active' => 0],
            */
            ['id' => 150,'name' => 'Norfolk Island','code' => 'nf','active' => 1],
            /*
            ['id' => 151,'name' => 'Nigeria','code' => 'ng','active' => 0],
            ['id' => 152,'name' => 'Nicaragua','code' => 'ni','active' => 0],
            ['id' => 153,'name' => 'Netherlands','code' => 'nl','active' => 0],
            */
            ['id' => 154,'name' => 'Norway','code' => 'no','active' => 1],
            /*
            ['id' => 155,'name' => 'Nepal','code' => 'np','active' => 0],
            ['id' => 156,'name' => 'Nauru','code' => 'nr','active' => 0],
            ['id' => 157,'name' => 'Niue','code' => 'nu','active' => 0],
            ['id' => 158,'name' => 'New Zealand (Aotearoa)','code' => 'nz','active' => 0],
            ['id' => 159,'name' => 'Oman','code' => 'om','active' => 0],
            ['id' => 160,'name' => 'Panama','code' => 'pa','active' => 0],
            ['id' => 161,'name' => 'Peru','code' => 'pe','active' => 0],
            ['id' => 162,'name' => 'French Polynesia','code' => 'pf','active' => 0],
            ['id' => 163,'name' => 'Papua New Guinea','code' => 'pg','active' => 0],
            ['id' => 164,'name' => 'Philippines','code' => 'ph','active' => 0],
            ['id' => 165,'name' => 'Pakistan','code' => 'pk','active' => 0],
            ['id' => 166,'name' => 'Poland','code' => 'pl','active' => 0],
            ['id' => 167,'name' => 'Saint Pierre and Miquelon','code' => 'pm','active' => 0],
            ['id' => 168,'name' => 'Pitcairn','code' => 'pn','active' => 0],
            ['id' => 169,'name' => 'Palestinian Territory','code' => 'ps','active' => 0],
            ['id' => 170,'name' => 'Portugal','code' => 'pt','active' => 0],
            ['id' => 171,'name' => 'Palau','code' => 'pw','active' => 0],
            ['id' => 172,'name' => 'Paraguay','code' => 'py','active' => 0],
            ['id' => 173,'name' => 'Qatar','code' => 'qa','active' => 0],
            ['id' => 174,'name' => 'Reunion','code' => 're','active' => 0],
            */
            ['id' => 175,'name' => 'Romania','code' => 'ro','active' => 1],
            /*
            ['id' => 176,'name' => 'Russian Federation','code' => 'ru','active' => 0],
            ['id' => 177,'name' => 'Rwanda','code' => 'rw','active' => 0],
            ['id' => 178,'name' => 'Saudi Arabia','code' => 'sa','active' => 0],
            ['id' => 179,'name' => 'Solomon Islands','code' => 'sb','active' => 0],
            ['id' => 180,'name' => 'Seychelles','code' => 'sc','active' => 0],
            ['id' => 181,'name' => 'Sudan','code' => 'sd','active' => 0],
            */
            ['id' => 182,'name' => 'Sweden','code' => 'se','active' => 1],
            /*
            ['id' => 183,'name' => 'Singapore','code' => 'sg','active' => 0],
            ['id' => 184,'name' => 'Saint Helena','code' => 'sh','active' => 0],
            ['id' => 185,'name' => 'Slovenia','code' => 'si','active' => 0],
            ['id' => 186,'name' => 'Svalbard and Jan Mayen','code' => 'sj','active' => 0],
            ['id' => 187,'name' => 'Slovakia','code' => 'sk','active' => 0],
            ['id' => 188,'name' => 'Sierra Leone','code' => 'sl','active' => 0],
            ['id' => 189,'name' => 'San Marino','code' => 'sm','active' => 0],
            ['id' => 190,'name' => 'Senegal','code' => 'sn','active' => 0],
            ['id' => 191,'name' => 'Somalia','code' => 'so','active' => 0],
            ['id' => 192,'name' => 'Suriname','code' => 'sr','active' => 0],
            ['id' => 193,'name' => 'Sao Tome and Principe','code' => 'st','active' => 0],
            ['id' => 194,'name' => 'El Salvador','code' => 'sv','active' => 0],
            ['id' => 195,'name' => 'Syria','code' => 'sy','active' => 0],
            ['id' => 196,'name' => 'Swaziland','code' => 'sz','active' => 0],
            ['id' => 197,'name' => 'Turks and Caicos Islands','code' => 'tc','active' => 0],
            ['id' => 198,'name' => 'Chad','code' => 'td','active' => 0],
            ['id' => 199,'name' => 'French Southern Territories','code' => 'tf','active' => 0],
            ['id' => 200,'name' => 'Togo','code' => 'tg','active' => 0],
            ['id' => 201,'name' => 'Thailand','code' => 'th','active' => 0],
            ['id' => 202,'name' => 'Tajikistan','code' => 'tj','active' => 0],
            ['id' => 203,'name' => 'Tokelau','code' => 'tk','active' => 0],
            ['id' => 204,'name' => 'Turkmenistan','code' => 'tm','active' => 0],
            ['id' => 205,'name' => 'Tunisia','code' => 'tn','active' => 0],
            ['id' => 206,'name' => 'Tonga','code' => 'to','active' => 0],
            ['id' => 207,'name' => 'Turkey','code' => 'tr','active' => 0],
            ['id' => 208,'name' => 'Trinidad and Tobago','code' => 'tt','active' => 0],
            ['id' => 209,'name' => 'Tuvalu','code' => 'tv','active' => 0],
            ['id' => 210,'name' => 'Taiwan','code' => 'tw','active' => 0],
            ['id' => 211,'name' => 'Tanzania','code' => 'tz','active' => 0],
            ['id' => 212,'name' => 'Ukraine','code' => 'ua','active' => 0],
            ['id' => 213,'name' => 'Uganda','code' => 'ug','active' => 0],
            ['id' => 214,'name' => 'Uruguay','code' => 'uy','active' => 0],
            ['id' => 215,'name' => 'Uzbekistan','code' => 'uz','active' => 0],
            ['id' => 216,'name' => 'Saint Vincent and the Grenadines','code' => 'vc','active' => 0],
            ['id' => 217,'name' => 'Venezuela','code' => 've','active' => 0],
            ['id' => 218,'name' => 'Virgin Islands (British)','code' => 'vg','active' => 0],
            ['id' => 219,'name' => 'Virgin Islands (U.S.)','code' => 'vi','active' => 0],
            ['id' => 220,'name' => 'Viet Nam','code' => 'vn','active' => 0],
            ['id' => 221,'name' => 'Vanuatu','code' => 'vu','active' => 0],
            ['id' => 222,'name' => 'Wallis and Futuna','code' => 'wf','active' => 0],
            ['id' => 223,'name' => 'Samoa','code' => 'ws','active' => 0],
            ['id' => 224,'name' => 'Yemen','code' => 'ye','active' => 0],
            ['id' => 225,'name' => 'Mayotte','code' => 'yt','active' => 0],
            ['id' => 226,'name' => 'South Africa','code' => 'za','active' => 0],
            ['id' => 227,'name' => 'Zambia','code' => 'zm','active' => 0],
            ['id' => 228,'name' => 'Zaire (former)','code' => 'zr','active' => 0],
            ['id' => 229,'name' => 'Zimbabwe','code' => 'zw','active' => 0],
            ['id' => 230,'name' => 'United States of America','code' => 'us','active' => 0],
            */
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
