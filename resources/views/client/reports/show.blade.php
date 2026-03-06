@extends('layouts.client')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-gray-800 p-6 rounded-xl">
        <h1 class="text-2xl font-bold text-white">{{ $report->summary_title }}</h1>
        <p class="text-gray-300 mt-2">Consult: {{ optional($report->consult_date)->toDayDateTimeString() ?? 'N/A' }}</p>

        <div class="mt-6 text-gray-200">
            <h3 class="font-semibold">Explanation</h3>
            <p class="mt-2">{{ $report->body_explanation }}</p>
        </div>

        <div class="mt-6 text-gray-200">
            <h3 class="font-semibold">Suggested Actions</h3>
            <p class="mt-2">{{ $report->body_suggested_actions }}</p>
        </div>

    </div>
</div>
@endsection