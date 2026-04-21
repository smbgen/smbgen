<?php

use Database\Seeders\ClientSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LeadFormSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\UserSeeder;

it('calls the expected core seeders', function () {
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
        ->and($seeder->calls[0])->toBe([
            UserSeeder::class,
            ClientSeeder::class,
            LeadFormSeeder::class,
            MessageSeeder::class,
        ]);
});
