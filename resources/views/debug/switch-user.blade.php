@extends('layouts.guest')

@section('title', 'Dev User Switcher')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 p-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-100 dark:bg-yellow-500/10 border border-yellow-300 dark:border-yellow-500/30 text-yellow-700 dark:text-yellow-400 text-xs font-mono mb-4">
                APP_DEBUG=true &mdash; local only
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dev User Switcher</h1>
            <p class="text-gray-600 dark:text-gray-500 text-sm mt-1">Click any user to log in instantly. No password required.</p>
        </div>

        @if(session('status'))
            <div class="mb-6 px-4 py-3 rounded-lg bg-green-100 dark:bg-green-500/10 border border-green-300 dark:border-green-500/20 text-green-700 dark:text-green-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @forelse($usersByRole as $role => $users)
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest
                    @if($role === 'super_admin') text-rose-600 dark:text-rose-400
                    @elseif($role === 'company_administrator') text-violet-600 dark:text-violet-400
                    @elseif($role === 'client') text-cyan-600 dark:text-cyan-400
                    @else text-gray-600 dark:text-gray-400
                    @endif">
                    {{ str_replace('_', ' ', $role) }}
                </span>
                <div class="flex-1 h-px bg-gray-200 dark:bg-white/5"></div>
                <span class="text-xs text-gray-500 dark:text-gray-600">{{ $users->count() }} {{ Str::plural('user', $users->count()) }}</span>
            </div>

            <div class="space-y-2">
                @foreach($users as $user)
                <a href="{{ route('debug.switch-user.post', $user) }}"
                    class="flex items-center justify-between px-4 py-3 rounded-xl border transition-all group
                        @if($role === 'super_admin') border-rose-200 dark:border-rose-500/20 bg-rose-50 dark:bg-rose-500/5 hover:border-rose-300 dark:hover:border-rose-500/40 hover:bg-rose-100 dark:hover:bg-rose-500/10
                        @elseif($role === 'company_administrator') border-violet-200 dark:border-violet-500/20 bg-violet-50 dark:bg-violet-500/5 hover:border-violet-300 dark:hover:border-violet-500/40 hover:bg-violet-100 dark:hover:bg-violet-500/10
                        @elseif($role === 'client') border-cyan-200 dark:border-cyan-500/20 bg-cyan-50 dark:bg-cyan-500/5 hover:border-cyan-300 dark:hover:border-cyan-500/40 hover:bg-cyan-100 dark:hover:bg-cyan-500/10
                        @else border-gray-200 dark:border-white/8 bg-gray-50 dark:bg-white/[0.03] hover:border-gray-300 dark:hover:border-white/15 hover:bg-gray-100 dark:hover:bg-white/[0.06]
                        @endif">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            @if($role === 'super_admin') bg-rose-200 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300
                            @elseif($role === 'company_administrator') bg-violet-200 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300
                            @elseif($role === 'client') bg-cyan-200 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300
                            @else bg-gray-200 dark:bg-gray-500/20 text-gray-700 dark:text-gray-300
                            @endif">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-600 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-gray-400 transition-colors">Login →</span>
                </a>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-500 dark:text-gray-600">
            <p class="text-lg mb-2">No users in the database.</p>
            <p class="text-sm">Run <code class="text-gray-700 dark:text-gray-400">php artisan db:seed</code> to create some.</p>
        </div>
        @endforelse

    </div>
</div>
@endsection
