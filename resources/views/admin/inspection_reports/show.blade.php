@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="bg-gray-800 p-6 rounded-xl">
        <h1 class="text-2xl font-bold text-white">{{ $report->summary_title }}</h1>
        <p class="text-gray-300 mt-2">Client: {{ $report->client_name }} | {{ $report->client_email }}</p>
        <div class="mt-4 text-gray-200">
            <h3 class="font-semibold">Explanation</h3>
            <p class="mt-2">{{ $report->body_explanation }}</p>
        </div>
        <div class="mt-6 text-gray-200">
            <h3 class="font-semibold">Suggested Actions</h3>
            <p class="mt-2">{{ $report->body_suggested_actions }}</p>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.inspection-reports.resend', $report->id) }}">
                @csrf
                <button class="bg-blue-600 px-4 py-2 rounded text-white hover:bg-blue-700 transition">
                    <i class="fas fa-envelope mr-2"></i>Resend Email
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.inspection-reports.store-to-drive', $report->id) }}">
                @csrf
                <button class="bg-green-600 px-4 py-2 rounded text-white hover:bg-green-700 transition">
                    <i class="fab fa-google-drive mr-2"></i>Store to Google Drive
                </button>
            </form>
            
            @if($report->google_drive_link)
                <a href="{{ $report->google_drive_link }}" target="_blank" class="bg-purple-600 px-4 py-2 rounded text-white hover:bg-purple-700 transition inline-block">
                    <i class="fas fa-external-link-alt mr-2"></i>View on Drive
                </a>
            @endif
            
            <a href="{{ route('reports.show', $report->id) }}" class="bg-gray-700 px-4 py-2 rounded text-white hover:bg-gray-600 transition inline-block">
                <i class="fas fa-eye mr-2"></i>View as Client
            </a>
        </div>
    </div>
</div>
@endsection