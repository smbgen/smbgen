<?php

namespace Database\Seeders;

use App\Models\LeadForm;
use Illuminate\Database\Seeder;

use function Laravel\Prompts\select;

class LeadFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Run: php artisan db:seed --class=LeadFormSeeder
     */
    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  LEAD FORM SEEDER');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        $businessType = select(
            label: 'What type of leads would you like to seed?',
            options: [
                'mold' => 'Mold Remediation / Water Damage Leads',
                'graphics' => 'Graphics Design / Branding Leads',
                'both' => 'Both Types',
                'custom' => 'Custom Number of Leads',
            ],
            default: 'both'
        );

        $count = 3; // Default per type

        if ($businessType === 'custom') {
            $count = (int) $this->command->ask('How many leads per type?', '3');
            $businessType = 'both';
        }

        if ($businessType === 'mold' || $businessType === 'both') {
            $this->seedMoldLeads($count);
        }

        if ($businessType === 'graphics' || $businessType === 'both') {
            $this->seedGraphicsLeads($count);
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  SEEDING COMPLETE');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
    }

    protected function seedMoldLeads(int $count): void
    {
        $this->command->info("Creating {$count} mold remediation leads...");

        $moldLeads = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'message' => 'Need mold inspection for basement. We had some water damage last month and noticed a musty smell. Can you come out and assess the situation?',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'message' => 'Water damage in kitchen from leaky pipe. Need assessment and remediation quote. Kitchen ceiling has water stains.',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@example.com',
                'message' => 'Bathroom renovation revealed black mold behind walls. Need professional removal and prevention advice.',
            ],
            [
                'name' => 'Jennifer Brown',
                'email' => 'jennifer.brown@example.com',
                'message' => 'Attic has visible mold growth after roof leak. Need immediate inspection and remediation services.',
            ],
            [
                'name' => 'Robert Martinez',
                'email' => 'robert.martinez@example.com',
                'message' => 'Flooded basement needs water extraction and mold prevention. How soon can you come out?',
            ],
            [
                'name' => 'Amanda Taylor',
                'email' => 'amanda.taylor@example.com',
                'message' => 'Musty odor in crawl space. Concerned about mold. Need professional assessment.',
            ],
        ];

        for ($i = 0; $i < $count && $i < count($moldLeads); $i++) {
            LeadForm::create(array_merge($moldLeads[$i], [
                'source_site' => 'website',
                'notification_email' => 'admin@clientbridge.app',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'referer' => 'https://clientbridge.app/landing',
            ]));
        }

        $this->command->info("✓ Created {$count} mold remediation leads");
    }

    protected function seedGraphicsLeads(int $count): void
    {
        $this->command->info("Creating {$count} graphics design leads...");

        $graphicsLeads = [
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@startup.com',
                'message' => 'Looking for logo design and brand identity for our new tech startup. Need something modern and professional.',
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@restaurant.com',
                'message' => 'Need complete rebrand for our restaurant. Logo, menu design, and social media graphics. Opening in 2 months.',
            ],
            [
                'name' => 'Lisa Rodriguez',
                'email' => 'lisa.rodriguez@consulting.com',
                'message' => 'Need business card design and letterhead for consulting firm. Clean, professional look preferred.',
            ],
            [
                'name' => 'Michael Thompson',
                'email' => 'michael.thompson@agency.com',
                'message' => 'Looking for social media graphics package. Need consistent branding across platforms.',
            ],
            [
                'name' => 'Jessica Lee',
                'email' => 'jessica.lee@boutique.com',
                'message' => 'Fashion boutique needs logo, packaging design, and hang tags. Elegant and minimalist style.',
            ],
            [
                'name' => 'Kevin O\'Brien',
                'email' => 'kevin.obrien@fitness.com',
                'message' => 'Gym rebranding project. Need logo, signage designs, and promotional materials.',
            ],
        ];

        for ($i = 0; $i < $count && $i < count($graphicsLeads); $i++) {
            LeadForm::create(array_merge($graphicsLeads[$i], [
                'source_site' => 'website',
                'notification_email' => 'admin@clientbridge.app',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'referer' => 'https://clientbridge.app/landing',
            ]));
        }

        $this->command->info("✓ Created {$count} graphics design leads");
    }
}
