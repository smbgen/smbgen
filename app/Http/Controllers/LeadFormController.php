<?php

namespace App\Http\Controllers;

use App\Mail\NewLeadSubmitted;
use App\Models\Client;
use App\Models\LeadForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadFormController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
            'source_site' => 'nullable|string|max:255',
            'notification_email' => 'nullable|email|max:255',
        ]);

        LeadForm::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'source_site' => $validated['source_site'] ?? $request->source,
            'notification_email' => $validated['notification_email'] ?? $request->input('notification_email'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
        ]);

        // Send email to ALL admin users
        try {
            $adminUsers = \App\Models\User::where('role', 'company_administrator')->get();

            if ($adminUsers->count() > 0) {
                foreach ($adminUsers as $admin) {
                    Mail::to($admin->email)->send(new NewLeadSubmitted($request->all()));
                }
            } else {
                // Fallback to config email if no admin users found
                $adminEmail = config('mail.admin_address', 'admin@clientbridge.app');
                Mail::to($adminEmail)->send(new NewLeadSubmitted($request->all()));
            }
        } catch (\Exception $e) {
            \Log::error('Lead form email failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            // Optionally flash a message if you want to let the user know something partial happened
            return redirect()->back()->with('failure', 'Mail sending failed. Please try again later.');
        }

        return redirect()->back()->with('success', 'Thanks for your interest in our services! We will reach out shortly.');
    }

    public function convert(Request $request, LeadForm $lead)
    {
        // Check if client with this email already exists
        $existing = Client::where('email', $lead->email)->first();

        if ($existing) {
            return redirect()->route('admin')->with('warning', 'This lead has already been converted to a client.');
        }
        // Validate and convert the lead to a client
        $clientData = $lead->only(['name', 'email', 'message', 'source_site']);
        $clientData['notification_email'] = $lead->notification_email ?? null;
        $client = Client::create($clientData);

        // Provision user account and send initial portal email
        try {
            \App\Services\ClientProvisionService::provision($client);
        } catch (\Exception $e) {
            \Log::error('Client provisioning failed during lead convert: '.$e->getMessage(), ['lead_id' => $lead->id, 'client_id' => $client->id]);

            return redirect()->route('admin')->with('success', 'Lead converted to client, but provisioning failed.');
        }

        return redirect()->route('admin')->with('success', 'Lead converted to client successfully.');
    }

    public function destroy(LeadForm $lead)
    {
        $lead->delete();

        return redirect()->route('admin')->with('success', 'Lead deleted successfully.');
    }

    public function partial()
    {
        $leads = \App\Models\LeadForm::latest()->take(50)->get();

        return view('partials.lead-table-rows', compact('leads'));
    }

    public function exportCsv()
    {
        $fileName = 'lead_form_submissions_'.now()->format('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // CSV Header row
            fputcsv($handle, [
                'ID', 'Name', 'Email', 'Message', 'Source Site', 'Notification Email', 'IP Address', 'User Agent', 'Referrer', 'Created At',
            ]);

            \App\Models\LeadForm::orderBy('created_at', 'desc')->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->message,
                        $row->source_site,
                        $row->notification_email,
                        $row->ip_address,
                        $row->user_agent,
                        $row->referer,
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
