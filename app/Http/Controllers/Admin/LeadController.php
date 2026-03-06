<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LeadForm;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of all leads.
     */
    public function index(Request $request)
    {
        $query = LeadForm::with('cmsPage')->latest();

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by source
        if ($request->has('source') && $request->source) {
            if ($request->source === 'cms') {
                $query->whereNotNull('cms_page_id');
            } elseif ($request->source === 'other') {
                $query->whereNull('cms_page_id');
            }
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->paginate(50);
        $totalLeads = LeadForm::count();
        $todayLeads = LeadForm::whereDate('created_at', today())->count();
        $cmsLeads = LeadForm::whereNotNull('cms_page_id')->count();

        return view('admin.leads.index', compact('leads', 'totalLeads', 'todayLeads', 'cmsLeads'));
    }

    /**
     * Display the specified lead.
     */
    public function show(LeadForm $lead)
    {
        $lead->load('cmsPage');

        // Check if already a client
        $existingClient = Client::where('email', $lead->email)->first();

        return view('admin.leads.show', compact('lead', 'existingClient'));
    }

    /**
     * Convert lead to client.
     */
    public function convertToClient(Request $request, LeadForm $lead)
    {
        // Check if client with this email already exists
        $existing = Client::where('email', $lead->email)->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'This lead has already been converted to a client.');
        }

        // Prepare client data
        $clientData = [
            'name' => $lead->name,
            'email' => $lead->email,
            'message' => $lead->message,
            'source_site' => $lead->source_site ?? ($lead->cmsPage ? $lead->cmsPage->slug : null),
            'notification_email' => $lead->notification_email,
        ];

        // If form_data contains property_address, add it
        if ($lead->form_data && isset($lead->form_data['property_address'])) {
            $clientData['property_address'] = $lead->form_data['property_address'];
        }

        // If form_data contains phone, add it
        if ($lead->form_data && isset($lead->form_data['phone'])) {
            $clientData['phone'] = $lead->form_data['phone'];
        } elseif ($lead->form_data && isset($lead->form_data['customer_phone'])) {
            $clientData['phone'] = $lead->form_data['customer_phone'];
        }

        $client = Client::create($clientData);

        // Provision user account and send initial portal email
        try {
            \App\Services\ClientProvisionService::provision($client);
        } catch (\Exception $e) {
            \Log::error('Client provisioning failed during lead convert: '.$e->getMessage(), [
                'lead_id' => $lead->id,
                'client_id' => $client->id,
            ]);

            return redirect()->route('admin.leads.index')->with('success', 'Lead converted to client, but provisioning failed.');
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead converted to client successfully.');
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(LeadForm $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully.');
    }

    /**
     * Export leads to CSV.
     */
    public function exportCsv(Request $request)
    {
        $fileName = 'leads_'.now()->format('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');

            // CSV Header row
            fputcsv($handle, [
                'ID', 'Name', 'Email', 'Message', 'Source', 'CMS Page', 'Notification Email',
                'IP Address', 'User Agent', 'Referrer', 'Created At',
            ]);

            $query = LeadForm::with('cmsPage')->orderBy('created_at', 'desc');

            // Apply same filters as index
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('source') && $request->source) {
                if ($request->source === 'cms') {
                    $query->whereNotNull('cms_page_id');
                } elseif ($request->source === 'other') {
                    $query->whereNull('cms_page_id');
                }
            }

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->message,
                        $row->source_site,
                        $row->cmsPage ? $row->cmsPage->title : 'N/A',
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

    /**
     * Toggle lead email notifications for the current user.
     */
    public function toggleNotifications(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'notify_on_new_leads' => $request->has('notify_on_new_leads'),
        ]);

        $message = $user->notify_on_new_leads
            ? 'You will now receive emails for new leads'
            : 'You will no longer receive emails for new leads';

        return redirect()->route('admin.leads.index')->with('success', $message);
    }
}
