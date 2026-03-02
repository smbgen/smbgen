<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ActivityLog::with('user')->latest();

            // Filter by action
            if ($request->filled('action')) {
                $query->ofAction($request->action);
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $query->byUser($request->user_id);
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->dateRange($request->start_date, $request->end_date);
            }

            // Search in description
            if ($request->filled('search')) {
                $query->where('description', 'like', '%'.$request->search.'%');
            }

            $logs = $query->paginate(50)->withQueryString();

            // Get unique actions for filter
            $actions = ActivityLog::select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action');

            // Get users for filter
            $users = User::orderBy('name')->get(['id', 'name', 'email']);

            // Get statistics
            $stats = [
                'total' => ActivityLog::count(),
                'today' => ActivityLog::whereDate('created_at', today())->count(),
                'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
            ];

            return view('admin.activity_logs.index', compact('logs', 'actions', 'users', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Activity logs error: '.$e->getMessage(), [
                'exception' => $e,
                'tenant' => tenant('id') ?? 'unknown',
            ]);

            // Return view with empty data and error message
            return view('admin.activity_logs.index', [
                'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50),
                'actions' => collect(),
                'users' => collect(),
                'stats' => [
                    'total' => 0,
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0,
                ],
            ])->with('error', 'Unable to load activity logs. The activity_logs table may not exist in this tenant database. Please run migrations: php artisan tenants:migrate');
        }
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return view('admin.activity_logs.show', compact('activityLog'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return back()->with('success', 'Activity log deleted successfully.');
    }

    public function clear(Request $request)
    {
        $request->validate([
            'older_than' => 'required|integer|min:1',
        ]);

        $deleted = ActivityLog::where('created_at', '<', now()->subDays($request->older_than))->delete();

        return back()->with('success', "Deleted {$deleted} activity logs older than {$request->older_than} days.");
    }
}
