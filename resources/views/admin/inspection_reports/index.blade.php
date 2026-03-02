@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-4">Inspection Reports</h1>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-white">Recent Reports</h2>
        <a href="{{ route('admin.inspection-reports.create') }}" class="bg-blue-600 px-4 py-2 rounded text-white">Create Report</a>
    </div>

    <div class="bg-gray-800 rounded-xl p-4">
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Title</th>
                    <th>Consult Date</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr class="border-t border-gray-700">
                        <td class="py-3">{{ $report->client_name }}</td>
                        <td>{{ $report->summary_title }}</td>
                        <td>{{ optional($report->consult_date)->format('M j, Y') }}</td>
                        <td>{{ $report->created_at->diffForHumans() }}</td>
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
