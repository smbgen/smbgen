<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin and client users
        $adminUser = User::where('role', 'company_administrator')->first();
        $clientUser = User::where('role', 'client')->first();

        if (! $adminUser || ! $clientUser) {
            return;
        }

        // Create 2 cyber audit oriented messages
        $messages = [
            [
                'sender_id' => $clientUser->id,
                'recipient_id' => $adminUser->id,
                'subject' => 'Urgent: Suspicious Network Activity Detected',
                'body' => "Hi there, I'm concerned about some unusual network activity we've been seeing. Our IT team noticed multiple failed login attempts and some files were accessed outside of business hours. We're a small accounting firm with sensitive client financial data, so this is really worrying us. Can you help us investigate this as soon as possible? We'd like to schedule a security assessment to make sure our systems are properly protected.",
                'is_read' => false,
                'created_at' => Carbon::now()->subDays(2)->setTime(9, 15),
            ],
            [
                'sender_id' => $adminUser->id,
                'recipient_id' => $clientUser->id,
                'subject' => 'Re: Urgent: Suspicious Network Activity Detected',
                'body' => "Thank you for reaching out about this security concern. This is exactly the type of situation where a professional cybersecurity assessment is crucial. I've reviewed your case and this definitely warrants immediate attention given the sensitive nature of your client data.\n\nI'd recommend we start with a comprehensive security audit that includes:\n• Network traffic analysis\n• User access review\n• Vulnerability assessment\n• Incident response planning\n\nI can schedule an emergency assessment for tomorrow morning. In the meantime, please ensure all employees change their passwords immediately and enable two-factor authentication if not already active.\n\nLet me know if you can meet tomorrow at 10 AM for the initial assessment.",
                'is_read' => true,
                'read_at' => Carbon::now()->subDays(1)->setTime(14, 30),
                'created_at' => Carbon::now()->subDays(1)->setTime(11, 45),
            ],
        ];

        foreach ($messages as $messageData) {
            Message::firstOrCreate([
                'sender_id' => $messageData['sender_id'],
                'recipient_id' => $messageData['recipient_id'],
                'subject' => $messageData['subject'],
                'created_at' => $messageData['created_at'],
            ], $messageData);
        }
    }
}
