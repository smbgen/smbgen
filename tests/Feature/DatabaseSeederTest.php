<?php

use Database\Seeders\ClientSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LeadFormSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\UserSeeder;

it('calls core seeders and skips missing optional module seeders', function () {
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
    $optionalSeeders = $seeder->calls[1] ?? [];

    expect($seeder->calls)->not->toBeEmpty()
        ->and($seeder->calls[0])->toBe([
            UserSeeder::class,
            ClientSeeder::class,
            LeadFormSeeder::class,
            MessageSeeder::class,
        ]);

    foreach ($optionalSeeders as $optionalSeeder) {
        expect(class_exists($optionalSeeder))->toBeTrue();
    }

    foreach ([
        \App\Modules\CleanSlate\Database\Seeders\DataBrokerSeeder::class,
        \App\Modules\CleanSlate\Database\Seeders\DemoCustomerSeeder::class,
    ] as $optionalSeederClass) {
        if (! class_exists($optionalSeederClass)) {
            expect($optionalSeeders)->not->toContain($optionalSeederClass);
        }
    }
});
