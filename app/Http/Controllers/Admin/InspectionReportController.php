<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInspectionReportRequest;
use App\Mail\InspectionReportMail;
use App\Models\InspectionReport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class InspectionReportController extends Controller
{
    public function store(StoreInspectionReportRequest $request)
    {
        $data = $request->validated();

        $report = InspectionReport::create([
            'client_id' => $data['client_id'] ?? null,
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'] ?? null,
            'client_email' => $data['client_email'] ?? null,
            'client_address' => $data['client_address'] ?? null,
            'consult_date' => $data['consult_date'] ?? null,
            'summary_title' => $data['summary_title'],
            'body_explanation' => $data['body_explanation'] ?? null,
            'body_suggested_actions' => $data['body_suggested_actions'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if (! empty($data['send_email']) && $report->client_email) {
            Mail::to($report->client_email)->send(new InspectionReportMail($report));
        }

        return redirect()->back()->with('success', 'Inspection report created.');
    }

    public function index()
    {
        // Protect against missing table (fresh installs / tests)
        if (! Schema::hasTable('inspection_reports')) {
            $reports = new LengthAwarePaginator([], 0, 20, 1, [
                'path' => request()->url(),
            ]);

            return view('admin.inspection_reports.index', compact('reports'));
        }

        try {
            $reports = InspectionReport::with('client')->latest()->paginate(20);
        } catch (\Exception $e) {
            // In case of other DB issues, gracefully return an empty paginator
            $reports = new LengthAwarePaginator([], 0, 20, 1, [
                'path' => request()->url(),
            ]);
        }

        return view('admin.inspection_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('admin.inspection_reports.create');
    }

    public function resend(InspectionReport $report)
    {
        if (! $report->client_email) {
            return redirect()->back()->with('warning', 'No client email available to send report.');
        }

        Mail::to($report->client_email)->send(new InspectionReportMail($report));

        return redirect()->back()->with('success', 'Report resent to client.');
    }

    public function show(InspectionReport $report)
    {
        return view('admin.inspection_reports.show', compact('report'));
    }

    public function storeToGoogleDrive(InspectionReport $report)
    {
        $user = auth()->user();

        // Check if user has Google OAuth token
        if (! $user->google_refresh_token) {
            return redirect()->back()->with('error', 'Please connect your Google account first. Visit the Calendar settings to connect.');
        }

        try {
            $driveService = new GoogleDriveService;

            // Find or create the Inspection Reports folder
            $folderResult = $driveService->findOrCreateInspectionReportsFolder($user->google_refresh_token);

            if (! $folderResult['success']) {
                return redirect()->back()->with('error', 'Failed to access Google Drive folder: '.$folderResult['error']);
            }

            // Store the report to Google Drive
            $result = $driveService->storeInspectionReport(
                $report,
                $user->google_refresh_token,
                $folderResult['folder_id']
            );

            if ($result['success']) {
                return redirect()->back()->with('success', 'Report successfully stored to Google Drive!');
            } else {
                return redirect()->back()->with('error', 'Failed to store report: '.$result['error']);
            }
        } catch (\Exception $e) {
            \Log::error('Google Drive storage failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'An error occurred: '.$e->getMessage());
        }
    }
}
