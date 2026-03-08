<?php

namespace App\Modules\CleanSlate\Database\Seeders;

use App\Models\User;
use App\Modules\CleanSlate\Models\DataBroker;
use App\Modules\CleanSlate\Models\Profile;
use App\Modules\CleanSlate\Models\RemovalRequest;
use App\Modules\CleanSlate\Models\ScanJob;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $brokers = DataBroker::where('active', true)->get();

        if ($brokers->isEmpty()) {
            $this->command->warn('No active brokers found — run DataBrokerSeeder first.');
            return;
        }

        $customers = [
            [
                'user' => [
                    'name'               => 'Sarah Mitchell',
                    'email'              => 'sarah.mitchell@demo.test',
                    'role'               => 'user',
                    'email_verified_at'  => now(),
                ],
                'subscription_tier' => 'professional',
                'profile' => [
                    'first_name'         => 'Sarah',
                    'last_name'          => 'Mitchell',
                    'date_of_birth'      => '1985-07-14',
                    'emails'             => ['sarah.mitchell@demo.test', 'smitchell85@gmail.com'],
                    'phones'             => ['301-555-0142'],
                    'addresses'          => [
                        ['street' => '4821 Oak Hill Dr', 'city' => 'Bethesda', 'state' => 'MD', 'zip' => '20814'],
                        ['street' => '220 W 45th St Apt 9C', 'city' => 'New York', 'state' => 'NY', 'zip' => '10036'],
                    ],
                    'onboarding_complete' => true,
                    'exposure_score'     => 72,
                ],
                'scenario' => 'high_exposure', // many findings, some removed
            ],
            [
                'user' => [
                    'name'               => 'Marcus Delgado',
                    'email'              => 'marcus.delgado@demo.test',
                    'role'               => 'user',
                    'email_verified_at'  => now(),
                ],
                'subscription_tier' => 'basic',
                'profile' => [
                    'first_name'         => 'Marcus',
                    'last_name'          => 'Delgado',
                    'date_of_birth'      => '1979-03-22',
                    'emails'             => ['marcus.delgado@demo.test'],
                    'phones'             => ['703-555-0198'],
                    'addresses'          => [
                        ['street' => '1150 S Arlington Ridge Rd', 'city' => 'Arlington', 'state' => 'VA', 'zip' => '22202'],
                    ],
                    'onboarding_complete' => true,
                    'exposure_score'     => 34,
                ],
                'scenario' => 'low_exposure', // few findings, mostly clean
            ],
            [
                'user' => [
                    'name'               => 'Jennifer Okafor',
                    'email'              => 'jennifer.okafor@demo.test',
                    'role'               => 'user',
                    'email_verified_at'  => now(),
                ],
                'subscription_tier' => 'executive',
                'profile' => [
                    'first_name'         => 'Jennifer',
                    'last_name'          => 'Okafor',
                    'date_of_birth'      => '1991-11-08',
                    'emails'             => ['jennifer.okafor@demo.test', 'j.okafor@work.test', 'jenniok91@yahoo.com'],
                    'phones'             => ['240-555-0167', '443-555-0212'],
                    'addresses'          => [
                        ['street' => '3300 Leisure World Blvd', 'city' => 'Silver Spring', 'state' => 'MD', 'zip' => '20906'],
                        ['street' => '800 N Glebe Rd Apt 412', 'city' => 'Arlington', 'state' => 'VA', 'zip' => '22203'],
                        ['street' => '9 W Mount Vernon Pl', 'city' => 'Baltimore', 'state' => 'MD', 'zip' => '21201'],
                    ],
                    'onboarding_complete' => true,
                    'exposure_score'     => 88,
                ],
                'scenario' => 'very_high_exposure', // extensive findings, removals in progress
            ],
        ];

        foreach ($customers as $data) {
            // Create or update the user
            $user = User::updateOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], ['password' => Hash::make('password')])
            );

            // Create a fake Cashier subscription (no real Stripe call)
            DB::table('subscriptions')->updateOrInsert(
                ['user_id' => $user->id, 'type' => 'default'],
                [
                    'stripe_id'     => 'sub_demo_' . Str::random(14),
                    'stripe_status' => 'active',
                    'stripe_price'  => 'price_demo_' . $data['subscription_tier'],
                    'quantity'      => 1,
                    'trial_ends_at' => null,
                    'ends_at'       => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );

            // Create profile
            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profile'], ['user_id' => $user->id])
            );

            // Wipe existing scan/removal data for this profile on re-seed
            ScanJob::where('profile_id', $profile->id)->delete();
            RemovalRequest::where('profile_id', $profile->id)->delete();

            // Seed scan jobs + removal requests based on scenario
            $this->seedScenario($profile, $brokers, $data['scenario']);

            $this->command->line("  Seeded: {$user->name} <{$user->email}> [{$data['subscription_tier']}]");
        }

        $this->command->info('Demo customers seeded.');
    }

    private function seedScenario(Profile $profile, $brokers, string $scenario): void
    {
        foreach ($brokers as $broker) {
            [$status, $listingsFound] = $this->pickScanOutcome($scenario);

            $job = ScanJob::create([
                'profile_id'     => $profile->id,
                'data_broker_id' => $broker->id,
                'status'         => $status,
                'listings_found' => $listingsFound,
                'scanned_at'     => $status !== 'pending' ? now()->subDays(rand(1, 14)) : null,
            ]);

            // Create removal requests for jobs with findings
            if ($listingsFound > 0 && $status === 'completed') {
                RemovalRequest::create([
                    'profile_id'     => $profile->id,
                    'data_broker_id' => $broker->id,
                    'status'         => $this->pickRemovalStatus($scenario),
                    'submitted_at'   => now()->subDays(rand(1, 10)),
                    'confirmed_at'   => $this->pickRemovalStatus($scenario) === 'confirmed' ? now()->subDays(rand(0, 5)) : null,
                    'notes'          => null,
                ]);
            }
        }
    }

    private function pickScanOutcome(string $scenario): array
    {
        return match ($scenario) {
            'high_exposure' => match (rand(0, 9)) {
                0       => ['pending',   0],
                1       => ['running',   0],
                2       => ['failed',    0],
                default => ['completed', rand(0, 3) > 0 ? rand(1, 4) : 0],
            },
            'low_exposure' => match (rand(0, 9)) {
                0, 1    => ['pending',   0],
                2       => ['running',   0],
                default => ['completed', rand(0, 4) > 0 ? 0 : rand(1, 2)],
            },
            'very_high_exposure' => match (rand(0, 9)) {
                0       => ['failed',    0],
                1       => ['running',   0],
                default => ['completed', rand(0, 2) > 0 ? rand(1, 6) : 0],
            },
            default => ['completed', 0],
        };
    }

    private function pickRemovalStatus(string $scenario): string
    {
        return match ($scenario) {
            'high_exposure'      => ['pending', 'submitted', 'confirmed', 'confirmed'][rand(0, 3)],
            'low_exposure'       => ['submitted', 'confirmed', 'confirmed'][rand(0, 2)],
            'very_high_exposure' => ['pending', 'pending', 'submitted', 'submitted', 'confirmed', 'failed'][rand(0, 5)],
            default              => 'pending',
        };
    }
}
