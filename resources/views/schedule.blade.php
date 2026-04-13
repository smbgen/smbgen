@extends('layouts.guest')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-2">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-semibold text-gray-100 mb-0">Schedule a Meeting</h2>
        <a href="{{ route('dashboard') }}" class="btn-secondary btn-sm">Back</a>
    </div>

    <div class="bg-blue-900/20 border border-blue-500 text-blue-300 px-6 py-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
            <div>
                <h5 class="font-semibold text-blue-200 mb-2">Scheduling coming soon</h5>
                <p class="text-blue-300">We're integrating a built-in scheduling system. Please contact us directly to book a meeting.</p>
            </div>
        </div>
    </div>
</div>
@endsection