<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <script>
        (function() {
            var theme = localStorage.getItem('theme-preference');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demo — {{ config('app.name', 'smbgen') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite('resources/js/app.js')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-4xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ config('app.name', 'smbgen') }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                    Demo
                </span>
            </div>
            <x-dark-mode-toggle class="text-sm px-2 py-1.5" />
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-6 py-16">
        <div class="w-full max-w-3xl">

            <!-- Hero -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Explore the platform
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                    Choose a perspective to explore. All features are available — no sign-up required.
                    Demo data resets every hour.
                </p>
            </div>

            @if ($errors->has('demo'))
                <div class="mb-8 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4 text-sm text-red-700 dark:text-red-300 text-center">
                    {{ $errors->first('demo') }}
                </div>
            @endif

            <!-- Role Selection Cards -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Admin Card -->
                <form method="POST" action="{{ route('demo.login', 'admin') }}">
                    @csrf
                    <button type="submit" class="group w-full text-left rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-8 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200 cursor-pointer">
                        <div class="mb-5">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/60 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            View as Admin
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Explore the full admin panel — manage clients, bookings, invoices, content, and settings.
                        </p>
                        <div class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:gap-2.5 transition-all">
                            Enter Admin Panel
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </form>

                <!-- Client Card -->
                <form method="POST" action="{{ route('demo.login', 'client') }}">
                    @csrf
                    <button type="submit" class="group w-full text-left rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-8 shadow-sm hover:shadow-md hover:border-violet-300 dark:hover:border-violet-600 transition-all duration-200 cursor-pointer">
                        <div class="mb-5">
                            <div class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 group-hover:bg-violet-200 dark:group-hover:bg-violet-900/60 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                            View as Client
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Experience the client portal — view invoices, messages, bookings, and your personal dashboard.
                        </p>
                        <div class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 group-hover:gap-2.5 transition-all">
                            Enter Client Portal
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>
                </form>

                <!-- Frontend Interfaces Card -->
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-8 shadow-sm hover:shadow-md hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-200">
                    <div class="mb-5">
                        <div class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 9h19.5" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        View Frontend Interfaces
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6">
                        Preview the public Contact, Book, and Pay experiences without entering a demo account.
                    </p>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 transition-colors hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200 dark:hover:bg-emerald-900/40">
                            Open Contact Interface
                        </a>
                        @if(config('business.features.booking'))
                            <a href="{{ route('booking.wizard') }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 transition-colors hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200 dark:hover:bg-emerald-900/40">
                                Open Book Interface
                            </a>
                        @endif
                        <a href="{{ route('payment.collect') }}" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 transition-colors hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200 dark:hover:bg-emerald-900/40">
                            Open Pay Interface
                        </a>
                    </div>
                </div>

            </div>

            <!-- Footer note -->
            <p class="mt-10 text-center text-sm text-gray-400 dark:text-gray-600">
                Demo data resets every hour. Changes you make will not persist.
            </p>

        </div>
    </main>

</body>
</html>
