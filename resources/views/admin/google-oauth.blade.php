@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Flash Messages -->
    @if (session('status'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-300 dark:border-green-500 text-green-800 dark:text-green-300 px-6 py-4 rounded-lg mb-8 flex items-center">
            <i class="fas fa-check-circle text-green-400 mr-3 text-lg"></i>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-300 dark:border-green-500 text-green-800 dark:text-green-300 px-6 py-4 rounded-lg mb-8 flex items-center">
            <i class="fas fa-check-circle text-green-400 mr-3 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-500 text-red-800 dark:text-red-300 px-6 py-4 rounded-lg mb-8 flex items-center">
            <i class="fas fa-exclamation-circle text-red-400 mr-3 text-lg"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-600 via-yellow-600 to-blue-600 rounded-2xl p-8 mb-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3">
                        <i class="fab fa-google text-white text-3xl"></i>
                    </div>
                    Google OAuth Intelligence
                </h1>
                <p class="text-white/90 text-lg">Monitor and manage Google account integrations across your platform</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary bg-white/10 hover:bg-white/20 border-white/30 text-white backdrop-blur-sm">
                    <i class="fas fa-users mr-2"></i>User Management
                </a>
            </div>
        </div>
    </div>

    @php
        $totalGoogleCredentials = \App\Models\GoogleCredential::count();
        $usersWithGoogle = \App\Models\User::whereHas('googleCredential')->count();
        $usersWithoutGoogle = $users->count() - $usersWithGoogle;
        $activeConnections = \App\Models\GoogleCredential::whereNotNull('refresh_token')->count();
    @endphp

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Google Accounts</p>
                    <p class="text-white text-4xl font-bold">{{ $totalGoogleCredentials }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fab fa-google text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-blue-100 text-sm">
                <i class="fas fa-database mr-1"></i>OAuth credentials stored
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">Active Connections</p>
                    <p class="text-white text-4xl font-bold">{{ $activeConnections }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fas fa-plug text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-red-100 text-sm">
                <i class="fas fa-check-circle mr-1"></i>With access tokens
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Users Connected</p>
                    <p class="text-white text-4xl font-bold">{{ $usersWithGoogle }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fas fa-user-check text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-green-100 text-sm">
                @php $percentage = $users->count() > 0 ? round(($usersWithGoogle / $users->count()) * 100) : 0; @endphp
                <i class="fas fa-chart-line mr-1"></i>{{ $percentage }}% of total users
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">Not Connected</p>
                    <p class="text-white text-4xl font-bold">{{ $usersWithoutGoogle }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-4">
                    <i class="fas fa-user-slash text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 text-yellow-100 text-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i>Require Google OAuth setup
            </div>
        </div>
    </div>

    <!-- Modern Modal for Google OAuth Linking -->
    <!-- OAuth Connection Status Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="fas fa-network-wired text-blue-400"></i>
                OAuth Connection Status
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $users->count() }} total users
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @forelse ($users as $user)
            @php
                $hasGoogleCalendar = $user->googleCredential !== null;
                $hasGoogleOAuth = $user->google_id !== null;
                $googleCred = $user->googleCredential;
                $isFullyConnected = $hasGoogleCalendar && $hasGoogleOAuth;
                $isPartiallyConnected = $hasGoogleCalendar || $hasGoogleOAuth;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border {{ $isFullyConnected ? 'border-green-500' : ($isPartiallyConnected ? 'border-yellow-500' : 'border-gray-300 dark:border-gray-700') }}">
                <!-- User Header with Connection Status -->
                <div class="p-6 {{ $isFullyConnected ? 'bg-gradient-to-r from-green-50 dark:from-green-900/30 to-blue-50 dark:to-blue-900/30' : ($isPartiallyConnected ? 'bg-gradient-to-r from-yellow-50 dark:from-yellow-900/30 to-orange-50 dark:to-orange-900/30' : 'bg-gray-50 dark:bg-gray-900/50') }} border-b border-gray-300 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            @php
                                $initials = collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
                                $colors = ['bg-blue-500', 'bg-purple-500', 'bg-green-500', 'bg-yellow-500', 'bg-pink-500'];
                                $colorIndex = ord($initials[0] ?? 'A') % count($colors);
                            @endphp
                            <div class="relative">
                                <div class="w-16 h-16 rounded-full {{ $colors[$colorIndex] }} flex items-center justify-center text-white font-bold text-xl">
                                    {{ $initials }}
                                </div>
                                @if($isFullyConnected)
                                    <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-lg">
                                        <i class="fab fa-google text-green-600 text-sm"></i>
                                    </div>
                                @elseif($isPartiallyConnected)
                                    <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-lg">
                                        <i class="fab fa-google text-yellow-600 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 flex items-center text-sm mt-1">
                                    <i class="fas fa-envelope mr-2"></i>
                                    {{ $user->email }}
                                </p>
                                <div class="flex items-center mt-2 gap-2 flex-wrap">
                                    @if($user->role === 'company_administrator')
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-purple-500/20 text-purple-300 border border-purple-500">
                                            <i class="fas fa-user-shield mr-1"></i>Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-200">
                                            <i class="fas fa-user mr-1"></i>User
                                        </span>
                                    @endif
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            <i class="fas fa-check-circle mr-1"></i>Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            @if($isFullyConnected)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-500/20 text-green-300 rounded-lg border border-green-500">
                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-bold">FULLY CONNECTED</span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">OAuth + Calendar</div>
                            @elseif($hasGoogleOAuth && !$hasGoogleCalendar)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-yellow-500/20 text-yellow-300 rounded-lg border border-yellow-500">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                                    <span class="text-xs font-bold">OAUTH ONLY</span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">No Calendar</div>
                            @elseif($hasGoogleCalendar && !$hasGoogleOAuth)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 text-blue-300 rounded-lg border border-blue-500">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    <span class="text-xs font-bold">CALENDAR ONLY</span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Password Login</div>
                            @else
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-red-500/20 text-red-300 rounded-lg border border-red-500">
                                    <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                    <span class="text-xs font-bold">NOT CONNECTED</span>
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">No Google</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Google OAuth Details -->
                <div class="p-6">
                    <!-- Google OAuth Account Status -->
                    @if ($user->google_id)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <div class="bg-red-500/20 rounded-lg p-1.5">
                                    <i class="fab fa-google text-red-400"></i>
                                </div>
                                Google OAuth Account
                            </h4>
                            <div class="bg-gradient-to-r from-red-900/20 to-orange-900/20 rounded-lg p-4 border border-red-500/30">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Login Type</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-red-500/20 text-red-300 border border-red-500">
                                            <i class="fab fa-google mr-1"></i>
                                            Google OAuth Login
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Google ID</span>
                                        <code class="text-xs text-red-700 dark:text-red-300 bg-gray-100 dark:bg-gray-900 px-3 py-1 rounded font-mono">{{ $user->google_id }}</code>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Authentication</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-green-500/20 text-green-300 border border-green-500">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            Google SSO
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Google Calendar Credentials (New Location) -->
                    @if ($googleCred)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <div class="bg-blue-500/20 rounded-lg p-1.5">
                                    <i class="fas fa-calendar text-blue-400"></i>
                                </div>
                                Google Calendar Integration
                            </h4>
                            <div class="bg-gradient-to-r from-blue-900/20 to-purple-900/20 rounded-lg p-4 border border-blue-500/30">
                                <div class="space-y-3">
                                    @if($googleCred->external_account_email)
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Google Account</span>
                                        <code class="text-xs text-blue-700 dark:text-blue-300 bg-gray-100 dark:bg-gray-900 px-3 py-1 rounded font-mono">{{ $googleCred->external_account_email }}</code>
                                    </div>
                                    @endif
                                    
                                    @if($googleCred->calendar_id)
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Calendar ID</span>
                                        <code class="text-xs text-green-700 dark:text-green-300 bg-gray-100 dark:bg-gray-900 px-3 py-1 rounded font-mono">{{ $googleCred->calendar_id }}</code>
                                    </div>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400 uppercase tracking-wider">Status</span>
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full bg-green-500/20 text-green-300 border border-green-500">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Connected
                                        </span>
                                    </div>

                                    @if($googleCred->created_at)
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Connected</span>
                                        <span class="text-xs text-gray-700 dark:text-gray-300">{{ $googleCred->created_at->diffForHumans() }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($hasGoogleCalendar)
                            </div>
                        </div>
                    @endif

                    <!-- No Google Connection -->
                    @if (!$user->google_id && !$hasGoogleCalendar)
                        <div class="mb-4">
                            <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 text-center">
                                <div class="mb-3">
                                    <i class="fab fa-google text-gray-500 dark:text-gray-600 text-4xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-400 font-medium mb-1">No Google Connection</p>
                                <p class="text-gray-600 dark:text-gray-500 text-sm">This user has no Google OAuth login and no Google Calendar integration</p>
                            </div>
                        </div>
                    @elseif (!$hasGoogleCalendar && $user->google_id)
                        <div class="mb-4">
                            <div class="bg-yellow-900/20 rounded-lg p-4 border border-yellow-500/30 text-center">
                                <div class="mb-2">
                                    <i class="fas fa-calendar-times text-yellow-400 text-2xl"></i>
                                </div>
                                <p class="text-yellow-800 dark:text-yellow-300 text-sm font-medium">Google Calendar Not Connected</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">User has Google OAuth login but no calendar integration</p>
                            </div>
                        </div>
                    @elseif ($hasGoogleCalendar && !$user->google_id)
                        <div class="mb-4">
                            <div class="bg-blue-900/20 rounded-lg p-4 border border-blue-500/30 text-center">
                                <div class="mb-2">
                                    <i class="fas fa-key text-blue-400 text-2xl"></i>
                                </div>
                                <p class="text-blue-800 dark:text-blue-300 text-sm font-medium">Password-Based Login</p>
                                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">User has calendar integration but uses traditional password login</p>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-user-edit mr-2"></i>Edit User
                            </a>
                            
                            @if($hasGoogleCalendar)
                                <form method="POST" action="{{ route('admin.calendar.disconnect') }}" class="w-full" 
                                      onsubmit="return confirm('Are you sure you want to disconnect Google Calendar for {{ $user->name }}?');">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-unlink mr-2"></i>Disconnect Calendar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-20 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                    <div class="mb-6">
                        <i class="fab fa-google text-gray-500 dark:text-gray-600 text-6xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-300 mb-2">No Users Found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">There are no users in the system yet to monitor OAuth connections.</p>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-users mr-2"></i>Manage Users
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Login Attempts Section -->
    @if ($recentLoginAttempts->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
            <!-- Section Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-t-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-full">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Recent Login Attempts</h2>
                            <p class="text-purple-100">Monitoring external authentication activity</p>
                        </div>
                    </div>
                    <div class="bg-white/20 rounded-lg px-3 py-1">
                        <span class="text-white font-medium">{{ $recentLoginAttempts->count() }} attempts</span>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">User</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">Email</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">Provider</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">Google ID</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">IP Address</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300 text-sm">When</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($recentLoginAttempts as $attempt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-3">
                                            @if(optional($attempt->user)->name)
                                                <div class="bg-blue-600/20 p-2 rounded-full">
                                                    <i class="fas fa-user text-blue-400 text-sm"></i>
                                                </div>
                                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ optional($attempt->user)->name ?? 'Unknown' }}</span>
                                            @else
                                                <div class="bg-gray-600/20 p-2 rounded-full">
                                                    <i class="fas fa-user-slash text-gray-500 text-sm"></i>
                                                </div>
                                                <span class="text-gray-600 dark:text-gray-400 italic">Unknown User</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $attempt->email ?? '—' }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="bg-white rounded-full p-1">
                                                <i class="fab fa-google text-blue-600 text-sm"></i>
                                            </div>
                                            <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($attempt->provider) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <code class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs">{{ $attempt->provider_id }}</code>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-globe text-gray-500 text-sm"></i>
                                            <span class="text-gray-700 dark:text-gray-300 font-mono text-sm">{{ $attempt->ip_address ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if ($attempt->was_linked)
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>Linked
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation mr-1"></i>Unlinked
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-clock text-sm"></i>
                                            <span class="text-sm">{{ $attempt->created_at ? $attempt->created_at->diffForHumans() : 'Unknown' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>


</div>
@endsection
