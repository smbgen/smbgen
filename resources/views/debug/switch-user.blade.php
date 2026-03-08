@extends('layouts.guest')

@section('title', 'Dev User Switcher')

@section('content')
<div class="min-h-screen bg-gray-950 text-gray-100 p-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-xs font-mono mb-4">
                APP_DEBUG=true &mdash; local only
            </div>
            <h1 class="text-2xl font-bold text-white">Dev User Switcher</h1>
            <p class="text-gray-500 text-sm mt-1">Click any user to log in instantly. No password required.</p>
        </div>

        @if(session('status'))
            <div class="mb-6 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @forelse($usersByRole as $role => $users)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest
                    @if($role === 'company_administrator') text-violet-400
                    @elseif($role === 'client') text-cyan-400
                    @else text-gray-400
                    @endif">
                    {{ str_replace('_', ' ', $role) }}
                </span>
                <div class="flex-1 h-px bg-white/5"></div>
                <span class="text-xs text-gray-600">{{ $users->count() }} {{ Str::plural('user', $users->count()) }}</span>
            </div>

            <div class="space-y-2">
                @foreach($users as $user)
                <form action="{{ route('debug.switch-user', $user) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl border transition-all text-left group
                            @if($role === 'company_administrator') border-violet-500/20 bg-violet-500/5 hover:border-violet-500/40 hover:bg-violet-500/10
                            @elseif($role === 'client') border-cyan-500/20 bg-cyan-500/5 hover:border-cyan-500/40 hover:bg-cyan-500/10
                            @else border-white/8 bg-white/[0.03] hover:border-white/15 hover:bg-white/[0.06]
                            @endif">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                @if($role === 'company_administrator') bg-violet-500/20 text-violet-300
                                @elseif($role === 'client') bg-cyan-500/20 text-cyan-300
                                @else bg-gray-500/20 text-gray-300
                                @endif">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white group-hover:text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-600 group-hover:text-gray-400 transition-colors">Login →</span>
                    </button>
                </form>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-600">
            <p class="text-lg mb-2">No users in the database.</p>
            <p class="text-sm">Run <code class="text-gray-400">php artisan db:seed</code> to create some.</p>
        </div>
        @endforelse

    </div>
</div>
@endsection
