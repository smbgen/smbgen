<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\CmsPage;
use App\Models\EmailLog;
use App\Models\LeadForm;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\select;

class ThemeSeeder extends Seeder
{
    protected User $admin;

    protected User $demo;

    protected array $clients = [];

    protected int $multiplier = 1;

    /**
     * Seed the entire application with theme-specific data.
     *
     * Run: php artisan db:seed --class=ThemeSeeder
     */
    public function run(): void
    {
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  smbgen THEME SEEDER');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();

        $theme = select(
            label: 'What type of business would you like to seed?',
            options: [
                'mold' => 'Mold Remediation / Water Damage',
                'landscape' => 'Landscape / Lawn Care',
                'graphics' => 'Graphic Designer / Photography',
                'ai' => 'AI Consultant',
            ],
            default: 'mold'
        );

        $this->multiplier = (int) select(
            label: 'How much data would you like to generate?',
            options: [
                '1' => 'Normal (5-15 of each)',
                '10' => 'Medium (50-150 of each)',
                '20' => 'Large (100-300 of each)',
                '50' => 'Stress Test (250-750 of each)',
                '100' => 'Maximum (500-1500 of each)',
            ],
            default: '1'
        );

        $this->command->newLine();
        $this->command->info("Seeding data for: {$this->getThemeName($theme)}");
        $this->command->info("Multiplier: {$this->multiplier}x");
        $this->command->newLine();

        // Seed admin and client users
        $this->seedUsers($theme);

        // Seed clients
        $this->seedClients($theme);

        // Seed lead forms
        $this->seedLeads($theme);

        // Seed CMS pages
        $this->seedCmsPages($theme);

        // Seed messages
        $this->seedMessages($theme);

        // Seed bookings
        $this->seedBookings($theme);

        // Seed email logs
        $this->seedEmailLogs($theme);

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('  SEEDING COMPLETE');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('📧 User Accounts Created:');
        $this->command->info('  Admin: admin@smbgen.com');
        $this->command->info('  Password: password');
        $this->command->newLine();
        $this->command->info('  Demo Client: demo@smbgen.com');
        $this->command->info('  Password: password');
        $this->command->newLine();
    }

    protected function getThemeName(string $theme): string
    {
        return match ($theme) {
            'mold' => 'Mold Remediation / Water Damage',
            'landscape' => 'Landscape / Lawn Care',
            'graphics' => 'Graphic Designer / Photography',
            'ai' => 'AI Consultant',
        };
    }

    protected function seedUsers(string $theme): void
    {
        $this->command->info('Creating users...');

        $adminName = match ($theme) {
            'mold' => 'Mold Remediation Business Admin',
            'landscape' => 'Green Lawn Business Admin',
            'graphics' => 'Creative Studio Business Admin',
            'ai' => 'AI Solutions Business Admin',
        };

        $this->admin = User::updateOrCreate(
            ['email' => 'admin@smbgen.com'],
            [
                'name' => $adminName,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'company_administrator',
            ]
        );

        $this->demo = User::updateOrCreate(
            ['email' => 'demo@smbgen.com'],
            [
                'name' => 'Demo Client',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'client',
            ]
        );

        $this->command->info("✓ Created admin user: {$this->admin->email} (password: password)");
        $this->command->info("✓ Created demo client: {$this->demo->email} (password: password)");
    }

    protected function seedClients(string $theme): void
    {
        $this->command->info('Creating clients...');

        $count = rand(5, 10) * $this->multiplier;
        $hasPropertyAddress = in_array($theme, ['mold', 'landscape']);

        $notes = match ($theme) {
            'mold' => ['Basement moisture concerns', 'Kitchen water damage', 'Bathroom mold removal', 'Attic leak inspection needed', 'Crawl space moisture', 'Post-flood assessment', 'HVAC mold check'],
            'landscape' => ['Weekly lawn maintenance', 'Spring cleanup needed', 'Backyard redesign project', 'Tree removal and planting', 'Irrigation system repair', 'Monthly service plan', 'Garden bed renovation'],
            'graphics' => ['Tech startup branding', 'Restaurant rebrand', 'Business collateral', 'Social media graphics package', 'Website redesign assets', 'Logo refresh project', 'Marketing materials'],
            'ai' => ['Chatbot implementation', 'ML model development', 'Computer vision project', 'NLP text analysis', 'Predictive analytics', 'AI strategy consulting', 'Data pipeline setup'],
        };

        for ($i = 0; $i < $count; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $name = $firstName.' '.$lastName;
            $email = strtolower($firstName.'.'.$lastName.'@'.fake()->domainName());

            $clientData = [
                'name' => $name,
                'email' => $email,
                'phone' => fake()->numerify('555-0###'),
                'property_address' => $hasPropertyAddress ? fake()->streetAddress().', '.fake()->city() : null,
                'notes' => $notes[array_rand($notes)],
            ];

            $client = Client::updateOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
            $this->clients[] = $client;
        }

        // Always add demo client
        $demoClient = Client::updateOrCreate(
            ['email' => 'demo@smbgen.com'],
            [
                'name' => 'Demo Client',
                'phone' => '555-0999',
                'property_address' => $hasPropertyAddress ? '999 Demo Lane, Test City' : null,
                'notes' => 'Demo account',
            ]
        );
        $this->clients[] = $demoClient;

        $this->command->info('✓ Created '.count($this->clients).' clients');
    }

    protected function seedLeads(string $theme): void
    {
        $this->command->info('Creating lead forms...');

        $count = rand(8, 15) * $this->multiplier;

        $messageTemplates = match ($theme) {
            'mold' => [
                'Need mold inspection for {area}. We had some water damage and noticed a musty smell. Can you assess the situation?',
                'Water damage in {area} from leaky pipe. Need assessment and remediation quote.',
                '{area} renovation revealed black mold. Need professional removal and prevention advice.',
                'Visible mold growth in {area} after leak. Need immediate inspection services.',
                'Flooded {area} needs water extraction and mold prevention. How soon can you come out?',
                '{area} has moisture issues. Concerned about mold. Need professional assessment.',
            ],
            'landscape' => [
                'Looking for weekly lawn mowing and seasonal landscaping. {size} property with flower beds.',
                'Need spring cleanup, mulching, and new tree planting for {size} yard.',
                'Backyard redesign project. Want to add patio, flower gardens, and improve drainage.',
                'Property needs ongoing maintenance. {size} lot with grass and shrubs.',
                'Lawn aeration and overseeding needed. Also need fertilization program for {size} lawn.',
                'Interested in landscape design consultation for {size} property.',
            ],
            'graphics' => [
                'Looking for logo design and brand identity for {business}. Need something modern and professional.',
                'Need complete rebrand for {business}. Logo, marketing materials, and social media graphics.',
                'Need business card design and letterhead for {business}. Clean, professional look preferred.',
                'Looking for social media graphics package for {business}. Need consistent branding.',
                '{business} needs logo, packaging design, and marketing materials.',
                'Photography services needed for {business}. Product and promotional shots.',
            ],
            'ai' => [
                '{business} looking to implement AI solutions for customer service automation.',
                'Interested in machine learning model development for {business} predictive analytics.',
                '{business} needs AI strategy consultation. Exploring applications for our product.',
                '{business} wants to implement recommendation engine. Need help with implementation.',
                '{business} exploring AI for data analysis. Need consultation on model training.',
                'Custom ML solutions needed for {business}. Want to discuss project scope.',
            ],
        };

        $areas = ['basement', 'kitchen', 'bathroom', 'attic', 'crawl space', 'bedroom', 'living room'];
        $sizes = ['1/2 acre', '1 acre', '2 acre', 'large', 'small', 'medium'];
        $businesses = ['our startup', 'our restaurant', 'our consulting firm', 'our boutique', 'our agency', 'our company'];

        for ($i = 0; $i < $count; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $name = $firstName.' '.$lastName;
            $email = strtolower($firstName.'.'.$lastName.'@'.fake()->domainName());

            $messageTemplate = $messageTemplates[array_rand($messageTemplates)];
            $message = str_replace(
                ['{area}', '{size}', '{business}'],
                [$areas[array_rand($areas)], $sizes[array_rand($sizes)], $businesses[array_rand($businesses)]],
                $messageTemplate
            );

            LeadForm::create([
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'source_site' => fake()->randomElement(['website', 'facebook', 'google', 'referral']),
                'notification_email' => 'admin@smbgen.com',
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'referer' => fake()->url(),
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info("✓ Created {$count} lead form submissions");
    }

    protected function seedCmsPages(string $theme): void
    {
        $this->command->info('Creating CMS pages...');

        $count = rand(3, 6);

        $pageTitles = match ($theme) {
            'mold' => ['Mold Inspection Services', 'Water Damage Restoration', 'Mold Remediation', 'Emergency Services', 'Prevention Tips', 'Air Quality Testing'],
            'landscape' => ['Lawn Care Services', 'Landscape Design', 'Seasonal Services', 'Irrigation Systems', 'Tree Services', 'Hardscaping'],
            'graphics' => ['Brand Identity', 'Photography Services', 'Design Process', 'Print Design', 'Digital Marketing', 'Portfolio'],
            'ai' => ['AI Consulting', 'Machine Learning', 'Training Workshops', 'Automation Services', 'Data Analytics', 'AI Strategy'],
        };

        $headDescriptions = match ($theme) {
            'mold' => ['Professional inspection services to protect your property', 'Fast response for water damage emergencies', 'Safe and effective mold removal solutions'],
            'landscape' => ['Keep your lawn green and healthy year-round', 'Transform your outdoor space with custom design', 'Complete property maintenance services'],
            'graphics' => ['Create a memorable brand that stands out', 'High-quality photography for your business', 'Professional design from concept to completion'],
            'ai' => ['Expert guidance on AI implementation', 'Custom ML models for your business needs', 'Hands-on training for your team'],
        };

        $ctaButtons = match ($theme) {
            'mold' => ['Schedule Inspection', 'Get Quote', 'Contact Us', 'Call Now'],
            'landscape' => ['Get Free Quote', 'Start Your Project', 'Contact Us', 'Schedule Service'],
            'graphics' => ['Start Your Brand', 'Book Session', 'View Portfolio', 'Get Started'],
            'ai' => ['Schedule Consultation', 'Discuss Your Project', 'Learn More', 'Get Started'],
        };

        $usedTitles = [];

        for ($i = 0; $i < $count; $i++) {
            // Ensure unique titles
            do {
                $title = $pageTitles[array_rand($pageTitles)];
            } while (in_array($title, $usedTitles) && count($usedTitles) < count($pageTitles));

            $usedTitles[] = $title;
            $slug = \Illuminate\Support\Str::slug($title);

            $headContent = $headDescriptions[array_rand($headDescriptions)];
            $bodyContent = '<h2>'.fake()->sentence(rand(3, 6)).'</h2>'
                .'<p>'.fake()->paragraphs(rand(2, 3), true).'</p>'
                .'<ul><li>'.implode('</li><li>', fake()->sentences(rand(3, 5))).'</li></ul>'
                .'<p>'.fake()->paragraph().'</p>';

            CmsPage::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'head_content' => $headContent,
                    'body_content' => $bodyContent,
                    'cta_text' => $ctaButtons[array_rand($ctaButtons)],
                    'cta_url' => '/contact',
                    'is_published' => fake()->boolean(85), // 85% published
                ]
            );
        }

        $this->command->info("✓ Created {$count} CMS pages");
    }

    protected function seedMessages(string $theme): void
    {
        $this->command->info('Creating messages...');

        $count = rand(5, 12) * $this->multiplier;

        $subjects = match ($theme) {
            'mold' => ['Inspection Report Ready', 'Follow-up: Water Damage', 'Invoice for Services', 'Appointment Reminder', 'Remediation Complete', 'Quote Request Follow-up'],
            'landscape' => ['Service Scheduled', 'Lawn Treatment Reminder', 'Design Proposal', 'Seasonal Cleanup', 'Invoice Attached', 'Maintenance Update'],
            'graphics' => ['Logo Concepts Ready', 'Brand Guidelines Delivered', 'Photo Shoot Schedule', 'Design Revisions', 'Project Complete', 'Invoice for Services'],
            'ai' => ['Assessment Complete', 'Model Performance Update', 'Training Materials', 'Project Milestone', 'Consultation Follow-up', 'Documentation Ready'],
        };

        $bodies = match ($theme) {
            'mold' => [
                'Your inspection report is ready for review. Please call to discuss remediation options.',
                'Just checking in after our service. How is everything? Let us know if you notice any issues.',
                'Thank you for choosing our services. Attached is your invoice for the work completed.',
                'This is a reminder about your scheduled appointment. We look forward to seeing you.',
                'The remediation work has been completed. Please review and let us know if you have questions.',
            ],
            'landscape' => [
                'We have you scheduled for next week. We\'ll handle all the work discussed.',
                'Your property is due for treatment. Please let us know your availability.',
                'Attached is the proposal for your project. Let\'s discuss the details.',
                'Time for seasonal maintenance. We can come out this week or next.',
                'Your invoice is attached. Thank you for your business.',
            ],
            'graphics' => [
                'I\'ve finished the concepts for your review. Let me know which direction you prefer!',
                'Your complete guidelines document is attached. This includes all usage specifications.',
                'Confirming our session. Please bring all items we discussed.',
                'Revisions are complete. Please review and provide any additional feedback.',
                'Your project is complete and ready for delivery. Files are attached.',
            ],
            'ai' => [
                'We\'ve completed the assessment. The full report is attached for your review.',
                'Great news! The model is performing well. Ready to discuss next steps.',
                'Attached are the materials from our session. Includes all examples and resources.',
                'We\'ve reached an important project milestone. Let\'s schedule a review call.',
                'Complete documentation is ready. This covers all implementation details.',
            ],
        };

        for ($i = 0; $i < $count; $i++) {
            $isRead = fake()->boolean(40); // 40% chance of being read
            $daysAgo = rand(1, 14);

            Message::create([
                'sender_id' => $this->admin->id,
                'recipient_id' => $this->demo->id,
                'subject' => $subjects[array_rand($subjects)],
                'body' => $bodies[array_rand($bodies)],
                'is_read' => $isRead,
                'read_at' => $isRead ? now()->subDays($daysAgo)->addHours(rand(1, 12)) : null,
                'created_at' => now()->subDays($daysAgo),
            ]);
        }

        $this->command->info("✓ Created {$count} messages");
    }

    protected function seedBookings(string $theme): void
    {
        $this->command->info('Creating bookings...');

        $count = rand(6, 12) * $this->multiplier;
        $hasPropertyAddress = in_array($theme, ['mold', 'landscape']);

        $notes = match ($theme) {
            'mold' => ['Basement moisture concerns', 'Kitchen water damage', 'Bathroom mold removal', 'Attic inspection needed', 'Crawl space moisture', 'Post-flood assessment'],
            'landscape' => ['Weekly maintenance plan', 'Full property cleanup', 'Backyard redesign', 'Tree removal and planting', 'Irrigation repair', 'Garden bed renovation'],
            'graphics' => ['Brand discovery session', 'Logo concepts review', 'Photo session prep', 'Design revisions meeting', 'Final delivery review', 'Project kickoff'],
            'ai' => ['Strategy consultation', 'ML model review', 'Training workshop', 'Implementation planning', 'Project scoping', 'Performance review'],
        };

        $times = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
        $durations = [30, 60, 90, 120, 180];

        for ($i = 0; $i < $count; $i++) {
            $daysAhead = rand(1, 30);
            $isConfirmed = fake()->boolean(30); // 30% confirmed

            Booking::create([
                'customer_name' => fake()->name(),
                'customer_email' => fake()->email(),
                'customer_phone' => fake()->numerify('555-0###'),
                'property_address' => $hasPropertyAddress ? fake()->streetAddress().', '.fake()->city() : null,
                'booking_date' => now()->addDays($daysAhead),
                'booking_time' => $times[array_rand($times)],
                'duration' => $durations[array_rand($durations)],
                'status' => $isConfirmed ? Booking::STATUS_CONFIRMED : Booking::STATUS_PENDING,
                'notes' => $notes[array_rand($notes)],
                'staff_id' => $this->admin->id,
            ]);
        }

        $this->command->info("✓ Created {$count} bookings");
    }

    protected function seedEmailLogs(string $theme): void
    {
        $this->command->info('Creating email logs...');

        $count = rand(15, 30) * $this->multiplier;

        $subjects = match ($theme) {
            'mold' => ['Inspection Confirmation', 'Service Quote', 'Remediation Complete', 'Follow-up Survey', 'Appointment Reminder', 'Invoice Attached'],
            'landscape' => ['Consultation Scheduled', 'Services Quote', 'Design Proposal', 'Service Reminder', 'Invoice Attached', 'Seasonal Update'],
            'graphics' => ['Project Kickoff', 'Concepts for Review', 'Brand Guidelines', 'Session Reminder', 'Revisions Complete', 'Project Delivered'],
            'ai' => ['Assessment Report', 'Model Documentation', 'Workshop Materials', 'Deployment Checklist', 'Performance Update', 'Consultation Follow-up'],
        };

        $statuses = ['sent', 'delivered', 'opened', 'clicked'];

        for ($i = 0; $i < $count; $i++) {
            $sentAt = now()->subHours(rand(1, 168)); // Past week
            $status = $statuses[array_rand($statuses)];

            EmailLog::create([
                'user_id' => $this->admin->id,
                'to_email' => fake()->email(),
                'subject' => $subjects[array_rand($subjects)],
                'body' => fake()->paragraph(3),
                'status' => $status,
                'sent_at' => $sentAt,
                'delivered_at' => in_array($status, ['delivered', 'opened', 'clicked']) ? $sentAt->copy()->addMinutes(rand(1, 5)) : null,
                'opened_at' => in_array($status, ['opened', 'clicked']) ? $sentAt->copy()->addMinutes(rand(10, 60)) : null,
                'clicked_at' => $status === 'clicked' ? $sentAt->copy()->addMinutes(rand(15, 90)) : null,
                'open_count' => in_array($status, ['opened', 'clicked']) ? rand(1, 5) : 0,
                'click_count' => $status === 'clicked' ? rand(1, 3) : 0,
                'tracking_id' => 'track_'.uniqid(),
            ]);
        }

        $this->command->info("✓ Created {$count} email logs");
    }
}
