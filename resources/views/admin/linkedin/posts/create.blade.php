@extends('layouts.admin')

@section('title', 'New LinkedIn Post')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.linkedin.posts.index') }}" class="text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-white">New LinkedIn Post</h1>
            <p class="text-gray-400 text-sm mt-0.5">Create and schedule a post for your LinkedIn business page.</p>
        </div>
    </div>

    {{-- Editor --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        @livewire('admin.social-post-editor', ['accounts' => $accounts])
    </div>

</div>
@endsection
