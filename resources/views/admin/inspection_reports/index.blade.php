@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Inspection Reports</h1>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Reports</h2>
        <a href="{{ route('admin.inspection-reports.create') }}" class="bg-blue-600 px-4 py-2 rounded text-white">Create Report</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="text-gray-700 dark:text-gray-300">Client</th>
                    <th class="text-gray-700 dark:text-gray-300">Title</th>
                    <th class="text-gray-700 dark:text-gray-300">Consult Date</th>
                    <th class="text-gray-700 dark:text-gray-300">Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="py-3 text-gray-900 dark:text-gray-100">{{ $report->client_name }}</td>
                        <td class="text-gray-900 dark:text-gray-100">{{ $report->summary_title }}</td>
                        <td class="text-gray-700 dark:text-gray-300">{{ optional($report->consult_date)->format('M j, Y') }}</td>
                        <td class="text-gray-700 dark:text-gray-300">{{ $report->created_at->diffForHumans() }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.inspection-reports.show', $report->id) }}" class="text-blue-400">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $reports->links() }}</div>
    </div>
</div>
@endsection
