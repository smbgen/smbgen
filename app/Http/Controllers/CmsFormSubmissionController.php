<?php

namespace App\Http\Controllers;

use App\Mail\CmsFormSubmissionAdminNotification;
use App\Mail\CmsFormSubmissionClientConfirmation;
use App\Models\CmsPage;
use App\Models\LeadForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CmsFormSubmissionController extends Controller
{
    /**
     * Handle form submission from a CMS page
     */
    public function submit(Request $request, string $slug): mixed
    {
        try {
            // Find the CMS page by slug
            $page = CmsPage::where('slug', $slug)
                ->where('is_published', true)
                ->where('has_form', true)
                ->firstOrFail();

            // Validate that the page has form fields configured
            if (empty($page->form_fields)) {
                return back()->with('error', 'This form is not properly configured.');
            }

            // Build validation rules dynamically from form_fields
            $rules = [];
            $messages = [];

            foreach ($page->form_fields as $field) {
                $fieldRules = [];

                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($field['type']) {
                    case 'email':
                        $fieldRules[] = 'email';
                        break;
                    case 'tel':
                        $fieldRules[] = 'string|max:20';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    case 'textarea':
                        $fieldRules[] = 'string|max:5000';
                        break;
                    default:
                        $fieldRules[] = 'string|max:255';
                }

                $rules[$field['name']] = implode('|', $fieldRules);

                if ($field['required'] ?? false) {
                    $messages[$field['name'].'.required'] = 'The '.($field['label'] ?? $field['name']).' field is required.';
                }
            }

            // Validate the request
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            // Map to standard LeadForm fields (only those that exist in DB)
            $leadData = [
                'cms_page_id' => $page->id,
                'name' => $validated['name'] ?? $validated['full_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'message' => $validated['message'] ?? $validated['comments'] ?? $validated['inquiry'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
            ];

            // Build form_data with ALL custom fields plus phone/property_address
            $formData = [];

            // Standard field names to exclude from form_data
            $standardFieldNames = ['name', 'full_name', 'email', 'message', 'comments', 'inquiry'];

            foreach ($validated as $key => $value) {
                if (! in_array($key, $standardFieldNames)) {
                    $formData[$key] = $value;
                }
            }

            $leadData['form_data'] = $formData;

            // Create the lead
            $lead = LeadForm::create($leadData);

            // Log the submission
            Log::info('CMS Form Submission', [
                'page_id' => $page->id,
                'page_slug' => $page->slug,
                'lead_id' => $lead->id,
                'email' => $lead->email,
            ]);

            // Send email notifications if enabled (catch errors to not block submission)
            try {
                $this->sendEmailNotifications($page, $lead, $validated);
            } catch (\Exception $e) {
                Log::error('Failed to send email notifications for form submission', [
                    'page_id' => $page->id,
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't throw - submission was successful even if emails failed
            }

            // Handle redirect or success message
            if ($page->form_redirect_url) {
                // Handle anchor links - redirect to current page with anchor
                if (str_starts_with($page->form_redirect_url, '#')) {
                    return back()->with('success', $page->form_success_message ?? 'Thank you for your submission!')
                        ->withFragment(ltrim($page->form_redirect_url, '#'));
                }

                // Handle relative URLs
                if (! str_starts_with($page->form_redirect_url, 'http')) {
                    return redirect($page->form_redirect_url)
                        ->with('success', $page->form_success_message ?? 'Thank you for your submission!');
                }

                // Handle absolute URLs
                return redirect()->away($page->form_redirect_url);
            }

            return back()->with('success', $page->form_success_message ?? 'Thank you for your submission!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('CMS form submission attempted for non-existent page', [
                'slug' => $slug,
                'ip' => $request->ip(),
            ]);

            return back()->with('error', 'Form not found.');
        } catch (\Exception $e) {
            Log::error('CMS Form Submission Error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'An error occurred while processing your submission. Please try again.')
                ->withInput();
        }
    }

    /**
     * Send email notifications for form submission
     *
     * Note: All emails are automatically logged via the LogSentEmail listener
     */
    protected function sendEmailNotifications(CmsPage $page, LeadForm $lead, array $formData): void
    {
        // Send admin notification
        if ($page->send_admin_notification && $page->notification_email) {
            try {
                $mailable = new CmsFormSubmissionAdminNotification($page, $lead, $formData);
                Mail::to($page->notification_email)->send($mailable);

                Log::info('CMS Form Admin Email Sent', [
                    'page_id' => $page->id,
                    'lead_id' => $lead->id,
                    'to' => $page->notification_email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send CMS form admin email', [
                    'page_id' => $page->id,
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send client confirmation
        if ($page->send_client_notification && $lead->email) {
            try {
                $mailable = new CmsFormSubmissionClientConfirmation($page, $lead, $formData);
                Mail::to($lead->email)->send($mailable);

                Log::info('CMS Form Client Email Sent', [
                    'page_id' => $page->id,
                    'lead_id' => $lead->id,
                    'to' => $lead->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send CMS form client email', [
                    'page_id' => $page->id,
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
