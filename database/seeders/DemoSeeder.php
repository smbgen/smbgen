<?php

namespace Database\Seeders;

use App\Http\Controllers\DemoController;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\BusinessSetting;
use App\Models\Client;
use App\Models\CmsNavbarSetting;
use App\Models\CmsPage;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LeadForm;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Wipe and re-seed all demo data.
     * This seeder is idempotent — safe to run multiple times.
     */
    public function run(): void
    {
        $this->cleanUp();
        $this->seedBusinessSettings();
        $this->seedCmsNavbarSettings();

        $adminUser = $this->seedUsers();
        $clientUser = $this->seedClientUser();

        $clients = $this->seedClients($clientUser);
        $this->seedBookings($clients);
        $this->seedInvoices($clientUser);
        $this->seedMessages($adminUser, $clientUser);
        $pages = $this->seedCmsPages();
        $this->seedLeads($pages);
        $this->seedBlogPosts($adminUser);
    }

    private function cleanUp(): void
    {
        // Remove demo users and all related records in safe order
        $demoEmails = [DemoController::DEMO_ADMIN_EMAIL, DemoController::DEMO_CLIENT_EMAIL];

        $demoUserIds = User::whereIn('email', $demoEmails)->pluck('id');

        if ($demoUserIds->isNotEmpty()) {
            Invoice::whereIn('user_id', $demoUserIds)->each(function (Invoice $invoice): void {
                $invoice->items()->delete();
                $invoice->delete();
            });

            Message::where(function ($q) use ($demoUserIds): void {
                $q->whereIn('sender_id', $demoUserIds)
                    ->orWhereIn('recipient_id', $demoUserIds);
            })->delete();

            BlogPost::whereIn('author_id', $demoUserIds)->delete();
        }

        Booking::where('customer_email', 'like', '%@demo.local')->delete();

        Client::where('email', 'like', '%@demo.local')->delete();

        LeadForm::where('email', 'like', '%@demo.local')->delete();

        CmsPage::where('slug', 'like', 'demo-%')
            ->orWhere('slug', 'home')
            ->delete();

        User::whereIn('email', $demoEmails)->delete();

        BusinessSetting::where('key', 'demo_company_name')->delete();
    }

    private function seedBusinessSettings(): void
    {
        BusinessSetting::set('company_name', 'Construction Co', 'string');
    }

    private function seedCmsNavbarSettings(): void
    {
        $companyName = config('business.name', config('business.company_name', config('app.company_name', config('app.name'))));

        CmsNavbarSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'logo_text' => $companyName,
            ]
        );
    }

    /**
     * Create the demo admin user and return it.
     */
    private function seedUsers(): User
    {
        return User::create([
            'name' => 'Alex (Demo Admin)',
            'email' => DemoController::DEMO_ADMIN_EMAIL,
            'password' => Hash::make('demo'),
            'role' => User::ROLE_ADMINISTRATOR,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create the demo client user and return it.
     */
    private function seedClientUser(): User
    {
        return User::create([
            'name' => 'Jordan (Demo Client)',
            'email' => DemoController::DEMO_CLIENT_EMAIL,
            'password' => Hash::make('demo'),
            'role' => User::ROLE_CLIENT,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Client>
     */
    private function seedClients(User $clientUser): \Illuminate\Database\Eloquent\Collection
    {
        // Create the demo client portal user's matching Client record
        Client::create([
            'name' => 'Jordan Smith',
            'email' => DemoController::DEMO_CLIENT_EMAIL,
            'phone' => '(555) 210-0001',
            'property_address' => '14 Oak Lane, Springfield, IL 62701',
            'is_active' => true,
            'account_activated_at' => now()->subDays(30),
        ]);

        // Additional demo clients
        $extras = [
            ['name' => 'Riverside Holdings LLC', 'email' => 'riverside@demo.local', 'phone' => '(555) 210-0002', 'property_address' => '8 Harbor Dr, Chicago, IL 60601'],
            ['name' => 'Green Leaf Properties', 'email' => 'greenleaf@demo.local', 'phone' => '(555) 210-0003', 'property_address' => '22 Elm St, Aurora, IL 60502'],
            ['name' => 'Blue Sky Ventures', 'email' => 'bluesky@demo.local', 'phone' => '(555) 210-0004', 'property_address' => '5 Skyway Blvd, Naperville, IL 60540'],
            ['name' => 'Summit Group Inc.', 'email' => 'summit@demo.local', 'phone' => '(555) 210-0005', 'property_address' => '1 Summit Pkwy, Joliet, IL 60431'],
            ['name' => 'Maple Street Realty', 'email' => 'maple@demo.local', 'phone' => '(555) 210-0006', 'property_address' => '77 Maple St, Rockford, IL 61101'],
            ['name' => 'Harborview Consulting', 'email' => 'harborview@demo.local', 'phone' => '(555) 210-0007', 'property_address' => '33 Lakeshore Dr, Evanston, IL 60201'],
            ['name' => 'Twin Pines Capital', 'email' => 'twinpines@demo.local', 'phone' => '(555) 210-0008', 'property_address' => '90 Pine Ave, Waukegan, IL 60085'],
        ];

        foreach ($extras as $data) {
            Client::create(array_merge($data, ['is_active' => true]));
        }

        return Client::whereIn('email', array_merge(
            [DemoController::DEMO_CLIENT_EMAIL],
            array_column($extras, 'email')
        ))->get();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Client>  $clients
     */
    private function seedBookings(\Illuminate\Database\Eloquent\Collection $clients): void
    {
        $upcomingDates = [
            now()->addDays(2)->format('Y-m-d'),
            now()->addDays(5)->format('Y-m-d'),
            now()->addDays(9)->format('Y-m-d'),
            now()->addDays(14)->format('Y-m-d'),
        ];

        $upcomingClients = $clients->take(4);

        foreach ($upcomingDates as $i => $date) {
            $client = $upcomingClients->get($i);
            Booking::factory()->create([
                'customer_name' => $client->name,
                'customer_email' => $client->email,
                'customer_phone' => $client->phone,
                'booking_date' => $date,
                'booking_time' => ['09:00:00', '10:30:00', '13:00:00', '15:30:00'][$i],
                'duration' => [60, 45, 60, 30][$i],
                'status' => Booking::STATUS_CONFIRMED,
                'notes' => ['Initial consultation', 'Follow-up review', 'Contract signing', 'Q&A session'][$i],
            ]);
        }

        // Past bookings
        $pastDates = [
            now()->subDays(7)->format('Y-m-d'),
            now()->subDays(14)->format('Y-m-d'),
            now()->subDays(21)->format('Y-m-d'),
        ];

        $pastClients = $clients->slice(4)->take(3)->values();

        foreach ($pastDates as $i => $date) {
            $client = $pastClients->get($i) ?? $clients->first();
            Booking::factory()->create([
                'customer_name' => $client->name,
                'customer_email' => $client->email,
                'customer_phone' => $client->phone,
                'booking_date' => $date,
                'booking_time' => '10:00:00',
                'status' => Booking::STATUS_CONFIRMED,
            ]);
        }
    }

    private function seedInvoices(User $clientUser): void
    {
        // Sent (pending) invoices
        $pendingData = [
            ['memo' => 'Monthly Retainer — May 2026', 'amount' => 150000, 'due_days' => 14],
            ['memo' => 'Strategy Session Package', 'amount' => 75000, 'due_days' => 7],
            ['memo' => 'Document Review & Filing', 'amount' => 40000, 'due_days' => 30],
        ];

        foreach ($pendingData as $data) {
            $invoice = Invoice::create([
                'user_id' => $clientUser->id,
                'status' => Invoice::STATUS_SENT,
                'currency' => 'usd',
                'memo' => $data['memo'],
                'due_date' => now()->addDays($data['due_days']),
                'total_amount' => $data['amount'],
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $data['memo'],
                'quantity' => 1,
                'unit_amount' => $data['amount'],
                'total_amount' => $data['amount'],
            ]);
        }

        // Paid invoices
        $paidData = [
            ['memo' => 'Monthly Retainer — April 2026', 'amount' => 150000, 'paid_days_ago' => 5],
            ['memo' => 'Onboarding & Setup Fee', 'amount' => 50000, 'paid_days_ago' => 30],
        ];

        foreach ($paidData as $data) {
            $invoice = Invoice::create([
                'user_id' => $clientUser->id,
                'status' => Invoice::STATUS_PAID,
                'currency' => 'usd',
                'memo' => $data['memo'],
                'due_date' => now()->subDays($data['paid_days_ago'] + 7),
                'total_amount' => $data['amount'],
                'paid_at' => now()->subDays($data['paid_days_ago']),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $data['memo'],
                'quantity' => 1,
                'unit_amount' => $data['amount'],
                'total_amount' => $data['amount'],
            ]);
        }
    }

    private function seedMessages(User $adminUser, User $clientUser): void
    {
        $conversations = [
            [
                'subject' => 'Welcome to your client portal',
                'body' => "Hi Jordan,\n\nWelcome aboard! Your client portal is now active. You can view invoices, book appointments, and send us messages here anytime.\n\nLet us know if you have any questions.\n\nBest,\nAlex",
                'sender' => $adminUser,
                'recipient' => $clientUser,
                'is_read' => true,
                'days_ago' => 30,
            ],
            [
                'subject' => 'Question about my invoice',
                'body' => "Hi Alex,\n\nI just noticed the April retainer invoice — can you confirm the payment was received? I sent it over last week.\n\nThanks,\nJordan",
                'sender' => $clientUser,
                'recipient' => $adminUser,
                'is_read' => true,
                'days_ago' => 6,
            ],
            [
                'subject' => 'Re: Question about my invoice',
                'body' => "Hi Jordan,\n\nYes — payment confirmed! The April retainer came through on the 20th. Your receipt is attached to the invoice in the portal.\n\nSee you at our session next week!\n\nAlex",
                'sender' => $adminUser,
                'recipient' => $clientUser,
                'is_read' => true,
                'days_ago' => 5,
            ],
            [
                'subject' => 'Rescheduling request',
                'body' => "Hi Alex,\n\nWould it be possible to move our May 5th appointment to the afternoon? Anytime after 2 PM works for me.\n\nThanks,\nJordan",
                'sender' => $clientUser,
                'recipient' => $adminUser,
                'is_read' => false,
                'days_ago' => 1,
            ],
        ];

        foreach ($conversations as $msg) {
            Message::create([
                'sender_id' => $msg['sender']->id,
                'recipient_id' => $msg['recipient']->id,
                'subject' => $msg['subject'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'read_at' => $msg['is_read'] ? now()->subDays($msg['days_ago'] - 1) : null,
                'created_at' => now()->subDays($msg['days_ago']),
                'updated_at' => now()->subDays($msg['days_ago']),
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, CmsPage>
     */
    private function seedCmsPages(): \Illuminate\Database\Eloquent\Collection
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => 'Construction Co - Built Right, Delivered On Time',
                'body_content' => '<section class="bg-slate-900 text-white"><div class="mx-auto max-w-6xl px-6 py-20"><p class="text-sm uppercase tracking-[0.2em] text-amber-300">General Contractor · Commercial & Residential</p><h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">Trusted Construction Partners for Projects That Cannot Slip</h1><p class="mt-6 max-w-3xl text-lg text-slate-200">Construction Co helps owners, developers, and property managers deliver high-quality builds with clear communication, disciplined schedules, and zero-surprise reporting.</p><div class="mt-8 flex flex-wrap gap-4"><a href="/contact" class="rounded-lg bg-amber-500 px-6 py-3 font-semibold text-slate-900 hover:bg-amber-400">Request a Site Walk</a><a href="/services" class="rounded-lg border border-slate-500 px-6 py-3 font-semibold text-white hover:bg-slate-800">View Services</a></div></div></section><section class="mx-auto grid max-w-6xl gap-6 px-6 py-14 md:grid-cols-3"><article class="rounded-xl border border-slate-200 p-6"><h2 class="text-xl font-semibold text-slate-900">Pre-Construction Planning</h2><p class="mt-3 text-slate-600">Scope alignment, budget modelling, and permitting coordination to reduce change orders before ground breaks.</p></article><article class="rounded-xl border border-slate-200 p-6"><h2 class="text-xl font-semibold text-slate-900">Ground-Up Construction</h2><p class="mt-3 text-slate-600">End-to-end project execution with dedicated supervision, milestone tracking, and quality control on every phase.</p></article><article class="rounded-xl border border-slate-200 p-6"><h2 class="text-xl font-semibold text-slate-900">Renovations & Tenant Improvements</h2><p class="mt-3 text-slate-600">Fast-turn interior upgrades designed to minimize downtime and keep your operations moving.</p></article></section><section class="bg-slate-50"><div class="mx-auto max-w-6xl px-6 py-12"><h3 class="text-2xl font-bold text-slate-900">Why teams choose Construction Co</h3><ul class="mt-4 grid gap-3 text-slate-700 md:grid-cols-2"><li>• Weekly owner updates with photo logs and schedule status</li><li>• Dedicated project manager and site superintendent</li><li>• Safety-first culture backed by documented procedures</li><li>• Transparent pricing and disciplined change management</li></ul></div></section>',
                'cta_text' => 'Request a Site Walk',
                'cta_url' => '/contact',
                'is_published' => true,
            ],
            [
                'slug' => 'demo-about',
                'title' => 'About Acme Services',
                'body_content' => '<div class="container mx-auto px-6 py-16"><h1 class="text-4xl font-bold mb-6">About Us</h1><p class="text-lg text-gray-600 mb-4">Acme Services Co. has been helping small and medium businesses grow since 2015. Our team of consultants specialises in operational efficiency, financial planning, and digital transformation.</p><p class="text-lg text-gray-600">We believe great service starts with listening — that\'s why every engagement begins with a complimentary discovery call.</p></div>',
                'cta_text' => 'Meet the Team',
                'cta_url' => '/contact',
                'is_published' => true,
            ],
            [
                'slug' => 'demo-services',
                'title' => 'Our Services',
                'body_content' => '<div class="container mx-auto px-6 py-16"><h1 class="text-4xl font-bold mb-10">What We Offer</h1><div class="grid md:grid-cols-3 gap-8"><div class="p-6 border rounded-xl"><h2 class="text-xl font-semibold mb-3">Strategy Consulting</h2><p class="text-gray-600">Structured planning sessions to align your team and sharpen your growth strategy.</p></div><div class="p-6 border rounded-xl"><h2 class="text-xl font-semibold mb-3">Operations Review</h2><p class="text-gray-600">End-to-end audit of your workflows, identifying bottlenecks and quick wins.</p></div><div class="p-6 border rounded-xl"><h2 class="text-xl font-semibold mb-3">Financial Planning</h2><p class="text-gray-600">Cash flow modelling, forecasting, and scenario planning to keep you on track.</p></div></div></div>',
                'cta_text' => 'Get Started',
                'cta_url' => '/book',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::create(array_merge($page, [
                'head_content' => '<meta name="description" content="'.$page['title'].'">',
                'background_color' => 'bg-white',
                'text_color' => 'text-gray-900',
                'show_navbar' => true,
                'has_form' => $page['slug'] !== 'demo-about',
                'form_fields' => $page['slug'] === 'demo-about' ? null : [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Full Name',
                        'placeholder' => 'Jordan Smith',
                        'required' => true,
                    ],
                    [
                        'type' => 'email',
                        'name' => 'email',
                        'label' => 'Work Email',
                        'placeholder' => 'jordan@example.com',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'message',
                        'label' => 'What do you need help with?',
                        'placeholder' => 'Tell us a bit about your goals.',
                        'required' => true,
                    ],
                ],
                'form_submit_button_text' => 'Request Consultation',
                'form_success_message' => 'Thanks for reaching out. We will follow up shortly.',
            ]));
        }

        return CmsPage::whereIn('slug', array_column($pages, 'slug'))->get()->keyBy('slug');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<string, CmsPage>  $pages
     */
    private function seedLeads(\Illuminate\Database\Eloquent\Collection $pages): void
    {
        $homePage = $pages->get('home');
        $servicesPage = $pages->get('demo-services');

        $leads = [
            [
                'cms_page_id' => $homePage?->id,
                'name' => 'Casey Morgan',
                'email' => 'casey.morgan@demo.local',
                'message' => 'We need help tightening up our monthly reporting and cash flow forecasting before summer.',
                'source_site' => 'website',
                'notification_email' => DemoController::DEMO_ADMIN_EMAIL,
                'ip_address' => '203.0.113.10',
                'user_agent' => 'Mozilla/5.0 Demo Browser',
                'referer' => 'https://demo.example.test/home',
                'form_data' => [
                    'company' => 'Morgan Property Group',
                    'timeline' => 'within_30_days',
                    'budget' => '$100k-$250k',
                ],
                'created_at' => now()->subDays(5),
            ],
            [
                'cms_page_id' => $servicesPage?->id,
                'name' => 'Taylor Nguyen',
                'email' => 'taylor.nguyen@demo.local',
                'message' => 'Looking for an operations review and a repeatable client onboarding process for our team.',
                'source_site' => 'landing_page',
                'notification_email' => DemoController::DEMO_ADMIN_EMAIL,
                'ip_address' => '203.0.113.22',
                'user_agent' => 'Mozilla/5.0 Demo Browser',
                'referer' => 'https://demo.example.test/demo-services',
                'form_data' => [
                    'team_size' => '12',
                    'priority' => 'operations',
                    'consultation_type' => 'strategy_call',
                ],
                'created_at' => now()->subDays(3),
            ],
            [
                'cms_page_id' => $servicesPage?->id,
                'name' => 'Riley Carter',
                'email' => 'riley.carter@demo.local',
                'message' => 'We are comparing providers and want a quote for ongoing financial planning support.',
                'source_site' => 'social_media',
                'notification_email' => DemoController::DEMO_ADMIN_EMAIL,
                'ip_address' => '203.0.113.35',
                'user_agent' => 'Mozilla/5.0 Demo Browser',
                'referer' => 'https://linkedin.com/company/construction-co-demo',
                'form_data' => [
                    'company' => 'Carter Warehousing',
                    'annual_revenue' => '$10M-$20M',
                    'needs_follow_up' => true,
                ],
                'created_at' => now()->subDay(),
            ],
            [
                'cms_page_id' => null,
                'name' => 'Jamie Patel',
                'email' => 'jamie.patel@demo.local',
                'message' => 'Referral from an existing client. Interested in a discovery call next week.',
                'source_site' => 'referral',
                'notification_email' => DemoController::DEMO_ADMIN_EMAIL,
                'ip_address' => '203.0.113.44',
                'user_agent' => 'Mozilla/5.0 Demo Browser',
                'referer' => null,
                'form_data' => [
                    'referred_by' => 'Jordan Smith',
                    'preferred_contact' => 'email',
                ],
                'created_at' => now()->subHours(6),
            ],
        ];

        LeadForm::withoutEvents(function () use ($leads): void {
            foreach ($leads as $lead) {
                LeadForm::create($lead + [
                    'updated_at' => $lead['created_at'],
                ]);
            }
        });
    }

    private function seedBlogPosts(User $adminUser): void
    {
        $posts = [
            [
                'title' => '5 Ways Small Businesses Can Improve Cash Flow Today',
                'slug' => 'demo-5-ways-improve-cash-flow',
                'excerpt' => 'Cash flow problems are the #1 reason small businesses fail. Here are five practical strategies you can implement this week.',
                'content' => '<p>Cash flow is the lifeblood of any small business. Even profitable companies can run into serious trouble when cash isn\'t available when it\'s needed.</p><h2>1. Invoice Promptly</h2><p>Every day you delay sending an invoice is a day your payment is delayed. Set up automatic invoicing the moment a project milestone is reached.</p><h2>2. Offer Early Payment Discounts</h2><p>A 2% discount for payment within 10 days can dramatically accelerate your receivables cycle.</p><h2>3. Review Your Recurring Expenses</h2><p>Audit your subscriptions and vendor contracts quarterly. Most businesses find 10–15% in savings with a focused review.</p><h2>4. Build a Cash Reserve</h2><p>Aim for 90 days of operating expenses in a dedicated account. Start small — even one month provides a meaningful buffer.</p><h2>5. Use Milestone Billing</h2><p>For larger projects, invoice at milestones rather than completion. This smooths your cash inflow and reduces collection risk.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'How to Prepare for Your First Business Consultation',
                'slug' => 'demo-prepare-for-consultation',
                'excerpt' => 'Getting the most from a consulting engagement starts long before the first meeting. Here\'s how to show up prepared.',
                'content' => '<p>A great consulting relationship starts with preparation. When you arrive at your first session with clear goals and good data, we can spend our time solving problems rather than gathering information.</p><h2>Bring Your Numbers</h2><p>Even rough financials — last 12 months of revenue, your top 5 expense categories, and current cash balance — give us a strong starting point.</p><h2>Know Your Goals</h2><p>What does success look like in 12 months? Write down your top three priorities before we meet. Specificity beats vagueness every time.</p><h2>List Your Frustrations</h2><p>What keeps you up at night? What processes feel broken? Your pain points are often the most valuable starting point for meaningful change.</p><h2>Come with Questions</h2><p>There are no bad questions. The more openly you engage, the more value you\'ll get from the engagement.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'The Case for a Client Portal in 2026',
                'slug' => 'demo-client-portal-2026',
                'excerpt' => 'Email chains and scattered files are costing you time and trust. Here\'s why a dedicated client portal is worth it.',
                'content' => '<p>If your client communication still lives in email threads and shared Dropbox folders, you\'re making your clients work harder than they should — and creating risk for your business.</p><h2>Centralised Communication</h2><p>A client portal keeps all messages, files, invoices, and bookings in one place. No more "can you resend that contract?" or "which invoice was that again?"</p><h2>Professionalism Signals Trust</h2><p>A branded, secure portal signals that you take your clients\' data seriously. It\'s a small thing that makes a meaningful impression.</p><h2>Self-Service Saves Time</h2><p>When clients can check invoice status, download receipts, or reschedule bookings without emailing you, you get hours back every week.</p><h2>Ready to See It in Action?</h2><p>You\'re using our demo right now. Everything you see — the portal, the invoices, the booking system — is available to your clients from day one.</p>',
                'status' => 'published',
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($posts as $postData) {
            BlogPost::create(array_merge($postData, [
                'author_id' => $adminUser->id,
                'content_blocks' => [],
                'seo_title' => $postData['title'],
                'seo_description' => $postData['excerpt'],
            ]));
        }
    }
}
