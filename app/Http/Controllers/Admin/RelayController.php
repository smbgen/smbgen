<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessEmailSequenceStepJob;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelayController extends Controller
{
    public function index(): View
    {
        $sequences = EmailSequence::withCount(['steps', 'enrollments'])->latest()->get();

        $stats = [
            'total' => $sequences->count(),
            'active' => $sequences->where('status', 'active')->count(),
            'enrolled' => EmailSequenceEnrollment::where('status', 'active')->count(),
        ];

        return view('admin.relay.index', compact('sequences', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'trigger' => ['required', 'in:lead_capture,client_created,manual'],
        ]);

        EmailSequence::create($validated);

        return back()->with('success', 'Sequence created.');
    }

    public function enroll(Request $request, EmailSequence $sequence): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'contact_name' => ['nullable', 'string', 'max:255'],
        ]);

        $enrollment = $sequence->enrollments()->create([
            ...$validated,
            'started_at' => now(),
            'current_step' => 0,
            'status' => 'active',
        ]);

        ProcessEmailSequenceStepJob::dispatch($enrollment);

        return back()->with('success', 'Contact enrolled in sequence.');
    }

    public function destroy(EmailSequence $sequence): RedirectResponse
    {
        $sequence->delete();

        return back()->with('success', 'Sequence deleted.');
    }
}
