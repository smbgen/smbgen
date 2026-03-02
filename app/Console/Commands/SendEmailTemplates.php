<?php

namespace App\Console\Commands;

use App\Mail\BookingConfirmation;
use App\Mail\ClientPortalAccessMail;
use App\Mail\CmsFormSubmissionAdminNotification;
use App\Mail\CmsFormSubmissionClientConfirmation;
use App\Mail\ContactInquiryReceived;
use App\Mail\InspectionReportMail;
use App\Mail\InvoiceMailable;
use App\Mail\MagicLinkMail;
use App\Mail\NewContactInquiry;
use App\Mail\NewLeadSubmitted;
use App\Mail\NewMessageReceived;
use App\Mail\ServerErrorNotification;
use App\Models\Booking;
use App\Models\CmsPage;
use App\Models\InspectionReport;
use App\Models\Invoice;
use App\Models\LeadForm;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:templates {email? : Email address to send templates to} {--template= : Send only specific template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all email templates with dummy data to a specified email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $specificTemplate = $this->option('template');

        // If no email provided, prompt for it
        if (! $email) {
            $email = $this->ask('Enter the email address to send templates to');
        }

        // Validate email
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email address: {$email}");

            return 1;
        }

        $this->info("📧 Sending email templates to: {$email}");
        $this->newLine();

        // Get list of available templates
        $templates = [
            'booking-confirmation' => 'Booking/Appointment Confirmation',
            'client-portal-access' => 'Client Portal Access Credentials',
            'cms-form-admin' => 'CMS Form Submission (Admin Notification)',
            'cms-form-client' => 'CMS Form Submission (Client Confirmation)',
            'contact-inquiry-received' => 'Contact Inquiry Received (Auto-reply)',
            'contact-inquiry-admin' => 'New Contact Inquiry (Admin Notification)',
            'inspection-report' => 'Inspection Report',
            'invoice' => 'Invoice with PDF',
            'magic-link' => 'Magic Login Link',
            'new-lead' => 'New Lead Submitted',
            'new-message' => 'New Message Received',
            'deployment' => 'Deployment Notification (see deploy:notify command)',
            'server-error' => 'Server Error Notification',
        ];

        // If specific template requested, validate it
        if ($specificTemplate) {
            if (! isset($templates[$specificTemplate])) {
                $this->error("Template '{$specificTemplate}' not found.");
                $this->info('Available templates:');
                foreach ($templates as $key => $name) {
                    $this->line("  • {$key} - {$name}");
                }

                return 1;
            }

            $this->info("Sending only: {$templates[$specificTemplate]}");
            $templatesToSend = [$specificTemplate => $templates[$specificTemplate]];
        } else {
            $templatesToSend = $templates;
        }

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($templatesToSend as $key => $name) {
            try {
                $this->line("Sending: {$name}...");

                switch ($key) {
                    case 'booking-confirmation':
                        $this->sendBookingConfirmation($email);
                        break;

                    case 'client-portal-access':
                        $this->sendClientPortalAccess($email);
                        break;

                    case 'cms-form-admin':
                        $this->sendCmsFormAdmin($email);
                        break;

                    case 'cms-form-client':
                        $this->sendCmsFormClient($email);
                        break;

                    case 'contact-inquiry-received':
                        $this->sendContactInquiryReceived($email);
                        break;

                    case 'contact-inquiry-admin':
                        $this->sendContactInquiryAdmin($email);
                        break;

                    case 'inspection-report':
                        $this->sendInspectionReport($email);
                        break;

                    case 'invoice':
                        $this->sendInvoice($email);
                        break;

                    case 'magic-link':
                        $this->sendMagicLink($email);
                        break;

                    case 'new-lead':
                        $this->sendNewLead($email);
                        break;

                    case 'new-message':
                        $this->sendNewMessage($email);
                        break;

                    case 'server-error':
                        $this->sendServerError($email);
                        break;

                    case 'deployment':
                        $this->comment('  ℹ️  Run: php artisan deploy:notify --to='.$email);
                        $skippedCount++;

                        continue 2;

                    default:
                        $this->warn("  ⚠️  Template not implemented: {$key}");
                        $skippedCount++;

                        continue 2;
                }

                $this->info('  ✅ Sent successfully');
                $successCount++;

            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$e->getMessage()}");
                $failedCount++;
            }

            // Small delay to avoid rate limiting
            usleep(500000); // 500ms
        }

        // Summary
        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("  ✅ Sent: {$successCount}");
        if ($failedCount > 0) {
            $this->line("  ❌ Failed: {$failedCount}");
        }
        if ($skippedCount > 0) {
            $this->line("  ⏭️  Skipped: {$skippedCount}");
        }

        return $failedCount > 0 ? 1 : 0;
    }

    private function sendBookingConfirmation(string $email): void
    {
        // Create dummy booking data
        $booking = new Booking([
            'customer_name' => 'John Doe',
            'customer_email' => $email,
            'customer_phone' => '(555) 123-4567',
            'booking_date' => now()->addDays(3),
            'booking_time' => '14:30',
            'duration' => 60,
            'notes' => 'Please bring property documents and ID.',
            'status' => Booking::STATUS_CONFIRMED,
            'timezone' => config('app.timezone', 'UTC'),
        ]);

        $mailable = new BookingConfirmation(
            booking: $booking,
            meetLink: 'https://meet.google.com/abc-defg-hij',
            staffName: 'Sarah Johnson'
        );

        Mail::to($email)->send($mailable);
    }

    private function sendClientPortalAccess(string $email): void
    {
        $mailable = new ClientPortalAccessMail(
            clientName: 'John Doe',
            emailAddress: $email,
            resetUrl: route('password.reset', ['token' => 'sample-reset-token-12345'])
        );

        Mail::to($email)->send($mailable);
    }

    private function sendCmsFormAdmin(string $email): void
    {
        $page = new CmsPage([
            'slug' => 'contact-us',
            'title' => 'Contact Us',
            'has_form' => true,
            'form_fields' => json_encode([
                ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel', 'required' => false],
                ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
            ]),
        ]);

        $lead = new LeadForm([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'I would like to know more about your premium service packages.',
            'source_site' => config('app.url'),
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        $formData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '(555) 987-6543',
            'message' => 'I would like to know more about your premium service packages.',
        ];

        $mailable = new CmsFormSubmissionAdminNotification($page, $lead, $formData);
        Mail::to($email)->send($mailable);
    }

    private function sendCmsFormClient(string $email): void
    {
        $page = new CmsPage([
            'slug' => 'contact-us',
            'title' => 'Contact Us',
            'has_form' => true,
            'form_success_message' => 'Thank you for contacting us! We will get back to you shortly.',
        ]);

        $lead = new LeadForm([
            'name' => 'Jane Smith',
            'email' => $email,
            'message' => 'I would like to know more about your premium service packages.',
            'source_site' => config('app.url'),
        ]);

        $formData = [
            'name' => 'Jane Smith',
            'email' => $email,
            'phone' => '(555) 987-6543',
            'message' => 'I would like to know more about your premium service packages.',
        ];

        $mailable = new CmsFormSubmissionClientConfirmation($page, $lead, $formData);
        Mail::to($email)->send($mailable);
    }

    private function sendContactInquiryReceived(string $email): void
    {
        $mailable = new ContactInquiryReceived(
            name: 'John Doe',
            companyName: 'Acme Corporation'
        );

        Mail::to($email)->send($mailable);
    }

    private function sendContactInquiryAdmin(string $email): void
    {
        $mailable = new NewContactInquiry(
            name: 'Jane Customer',
            email: 'jane@example.com',
            phone: '(555) 123-4567',
            preferredContact: 'email',
            message: 'I am interested in learning more about your services. Could we schedule a consultation?',
            submittedAt: now()->format('M j, Y \a\t g:i A T'),
            replyToEmail: 'jane@example.com',
            replyToName: 'Jane Customer'
        );

        Mail::to($email)->send($mailable);
    }

    private function sendInspectionReport(string $email): void
    {
        $report = new InspectionReport([
            'client_name' => 'John Doe',
            'client_email' => $email,
            'client_phone' => '(555) 123-4567',
            'client_address' => '123 Main St, Springfield, IL 62701',
            'consult_date' => now()->subDays(2),
            'summary_title' => 'Property Inspection Report',
            'body_explanation' => 'Overall property is in good condition. The structure appears sound with no major structural concerns. Minor wear and tear typical for a property of this age.',
            'body_suggested_actions' => '1. Minor roof repairs recommended in next 6 months\n2. Plumbing fixtures should be inspected\n3. HVAC system maintenance suggested',
            'google_drive_link' => 'https://drive.google.com/file/d/sample-file-id/view',
        ]);

        $mailable = new InspectionReportMail($report);

        Mail::to($email)->send($mailable);
    }

    private function sendInvoice(string $email): void
    {
        // Get or create a real user (needed for invoice relationships)
        $user = User::first();

        if (! $user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create a real invoice with items (required for PDF generation)
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-'.now()->format('Ymd-His'),
            'user_id' => $user->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'pending',
            'subtotal' => 1500.00,
            'tax' => 120.00,
            'total' => 1620.00,
            'notes' => 'Thank you for your business! (Test Invoice)',
        ]);

        // Add invoice items
        $invoice->items()->createMany([
            [
                'description' => 'Professional Consultation Services',
                'quantity' => 3,
                'unit_price' => 300.00,
                'total' => 900.00,
            ],
            [
                'description' => 'Technical Implementation',
                'quantity' => 2,
                'unit_price' => 250.00,
                'total' => 500.00,
            ],
            [
                'description' => 'Support & Maintenance (Monthly)',
                'quantity' => 1,
                'unit_price' => 100.00,
                'total' => 100.00,
            ],
        ]);

        $mailable = new InvoiceMailable($invoice);
        Mail::to($email)->send($mailable);

        // Clean up test invoice after sending
        $invoice->items()->delete();
        $invoice->delete();
    }

    private function sendMagicLink(string $email): void
    {
        $mailable = new MagicLinkMail(
            userName: 'John Doe',
            linkUrl: route('login').'?token=sample-magic-token-abc123xyz',
            expiresAt: now()->addHours(24)->format('M j, Y \a\t g:i A')
        );

        Mail::to($email)->send($mailable);
    }

    private function sendNewLead(string $email): void
    {
        $leadData = [
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '(555) 456-7890',
            'company' => 'Acme Corporation',
            'message' => 'Interested in enterprise solutions for our team of 50+ employees.',
            'source' => 'Website Contact Form',
            'created_at' => now(),
        ];

        $mailable = new NewLeadSubmitted($leadData);
        Mail::to($email)->send($mailable);
    }

    private function sendNewMessage(string $email): void
    {
        // Create dummy users
        $sender = User::first() ?? new User([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
        ]);

        $recipient = new User([
            'name' => 'Bob Johnson',
            'email' => $email,
        ]);

        // Create dummy message
        $message = new Message([
            'sender_id' => $sender->id ?? 1,
            'recipient_id' => $recipient->id ?? 2,
            'subject' => 'Project Update Required',
            'body' => "Hi Bob,\n\nI wanted to follow up on the project timeline we discussed last week. Could you provide an update on the current status?\n\nSpecifically, I'm interested in:\n- Progress on Phase 1 deliverables\n- Any blockers or concerns\n- Updated timeline for Phase 2\n\nLooking forward to your response.\n\nBest regards,\nAlice",
            'created_at' => now(),
        ]);

        // Set relations
        $message->setRelation('sender', $sender);
        $message->setRelation('recipient', $recipient);

        $mailable = new NewMessageReceived($message);
        Mail::to($email)->send($mailable);
    }

    private function sendServerError(string $email): void
    {
        // Create a mock exception
        $exception = new \Exception(
            'Class "App\Services\NonExistentService" not found in app/Http/Controllers/ExampleController.php:42'
        );

        $context = [
            'url' => 'https://example.com/dashboard',
            'user' => 'admin@example.com',
            'environment' => config('app.env'),
            'time' => now()->format('Y-m-d H:i:s T'),
        ];

        $mailable = new ServerErrorNotification(
            exception: $exception,
            context: $context
        );

        Mail::to($email)->send($mailable);
    }
}
