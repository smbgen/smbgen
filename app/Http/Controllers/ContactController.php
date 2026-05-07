<?php

namespace App\Http\Controllers;

use App\Mail\ContactInquiryReceived;
use App\Mail\NewContactInquiry;
use App\Models\LeadForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'preferred_contact' => 'nullable|string|in:email,phone,either',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Create a lead form entry
            $lead = LeadForm::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'message' => $validated['message']."\n\n".
                    'Phone: '.($validated['phone'] ?? 'Not provided')."\n".
                    'Preferred Contact: '.($validated['preferred_contact'] ?? 'Email'),
                'source_site' => 'Contact Page',
                'notification_email' => config('business.contact.email'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
            ]);

            // Send confirmation email to customer
            try {
                Mail::to($validated['email'])
                    ->send(new ContactInquiryReceived(
                        name: $validated['name'],
                        companyName: config('app.name'),
                    ));
            } catch (\Exception $e) {
                Log::warning('Failed to send customer confirmation email', [
                    'error' => $e->getMessage(),
                    'lead_id' => $lead->id,
                ]);
            }

            // Send notification email to business
            $businessEmail = config('business.contact.email');
            if ($businessEmail) {
                try {
                    Mail::to($businessEmail)
                        ->send(new NewContactInquiry(
                            name: $validated['name'],
                            email: $validated['email'],
                            phone: $validated['phone'] ?? null,
                            preferredContact: $validated['preferred_contact'] ?? null,
                            message: $validated['message'],
                            submittedAt: now()->format('F j, Y \a\t g:i A'),
                            replyToEmail: $validated['email'],
                            replyToName: $validated['name'],
                        ));
                } catch (\Exception $e) {
                    Log::warning('Failed to send admin notification email', [
                        'error' => $e->getMessage(),
                        'lead_id' => $lead->id,
                    ]);
                }
            }

            return redirect()->route('contact')->with('success', 'Thank you for contacting us! We\'ll get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return redirect()->route('contact')
                ->withInput()
                ->with('error', 'Sorry, there was an error submitting your message. Please try again or contact us directly.');
        }
    }
}
