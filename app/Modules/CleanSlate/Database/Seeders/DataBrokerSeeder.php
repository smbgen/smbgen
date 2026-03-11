<?php

namespace App\Modules\CleanSlate\Database\Seeders;

use App\Modules\CleanSlate\Models\DataBroker;
use Illuminate\Database\Seeder;

class DataBrokerSeeder extends Seeder
{
    public function run(): void
    {
        $brokers = [
            // Tier 1 — web_form
            ['name' => 'Spokeo',               'domain' => 'spokeo.com',               'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Whitepages',            'domain' => 'whitepages.com',            'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'PeopleFinder',          'domain' => 'peoplefinder.com',          'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Intelius',              'domain' => 'intelius.com',              'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'BeenVerified',          'domain' => 'beenverified.com',          'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Radaris',               'domain' => 'radaris.com',               'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Instant Checkmate',     'domain' => 'instantcheckmate.com',      'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'True People Search',    'domain' => 'truepeoplesearch.com',      'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Fast People Search',    'domain' => 'fastpeoplesearch.com',      'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'PeekYou',               'domain' => 'peekyou.com',               'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'MyLife',                'domain' => 'mylife.com',                'opt_out_method' => 'email',    'min_tier' => 1],
            ['name' => 'ZabaSearch',            'domain' => 'zabasearch.com',            'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Addresses.com',         'domain' => 'addresses.com',             'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'US Phone Book',         'domain' => 'usphonebook.com',           'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'AnyWho',                'domain' => 'anywho.com',                'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'PeopleSmart',           'domain' => 'peoplesmart.com',           'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => 'Dataveria',             'domain' => 'dataveria.com',             'opt_out_method' => 'web_form', 'min_tier' => 1],
            ['name' => '411',                   'domain' => '411.com',                   'opt_out_method' => 'web_form', 'min_tier' => 1],
            // Tier 2 — web_form
            ['name' => 'Public Records Now',    'domain' => 'publicrecordsnow.com',      'opt_out_method' => 'web_form', 'min_tier' => 2],
            ['name' => 'CheckPeople',           'domain' => 'checkpeople.com',           'opt_out_method' => 'web_form', 'min_tier' => 2],
            ['name' => 'TruthFinder',           'domain' => 'truthfinder.com',           'opt_out_method' => 'web_form', 'min_tier' => 2],
            ['name' => 'Background Check Run',  'domain' => 'backgroundcheck.run',       'opt_out_method' => 'web_form', 'min_tier' => 2],
            ['name' => 'Cyber Background Checks', 'domain' => 'cyberbackgroundchecks.com', 'opt_out_method' => 'web_form', 'min_tier' => 2],
            ['name' => 'IDCrawl',               'domain' => 'idcrawl.com',               'opt_out_method' => 'web_form', 'min_tier' => 2],
            // Tier 3 — manual
            ['name' => 'Private Records',       'domain' => 'privaterecords.net',        'opt_out_method' => 'manual',   'min_tier' => 3],
        ];

        foreach ($brokers as $broker) {
            DataBroker::updateOrCreate(
                ['domain' => $broker['domain']],
                array_merge($broker, ['active' => true])
            );
        }
    }
}
