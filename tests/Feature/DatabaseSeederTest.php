<?php

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserSeeder;

it('skips missing optional module seeders', function () {
    $seeder = new class extends DatabaseSeeder
    {
        public array $calls = [];

        public function call($class, $silent = false, array $parameters = [])
        {
            $this->calls[] = $class;

            return $this;
        }
    };

    $seeder->run();

    expect($seeder->calls)->toHaveCount(1)
        ->and($seeder->calls[0])->toContain(UserSeeder::class)
        ->and($seeder->calls[0])->not->toContain(\App\Modules\CleanSlate\Database\Seeders\DataBrokerSeeder::class)
        ->and($seeder->calls[0])->not->toContain(\App\Modules\CleanSlate\Database\Seeders\DemoCustomerSeeder::class);
});
