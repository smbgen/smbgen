@extends('layouts.super-admin')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="max-w-md text-center space-y-6">
        <div class="w-16 h-16 mx-auto bg-amber-900/30 rounded-full flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-amber-400 text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-semibold text-white">Setup Required</h1>
            <p class="text-gray-400 mt-2 text-sm leading-relaxed">
                The tenancy database tables are missing. Run migrations to initialize the multi-tenancy infrastructure before managing tenants.
            </p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-left">
            <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wider">Missing Tables</p>
            <ul class="space-y-1">
                @if (!$tenantsTableExists)
                    <li class="flex items-center gap-2 text-sm text-red-400">
                        <i class="fas fa-times-circle text-xs"></i> <code>tenants</code>
                    </li>
                @endif
                @if (!$domainsTableExists)
                    <li class="flex items-center gap-2 text-sm text-red-400">
                        <i class="fas fa-times-circle text-xs"></i> <code>domains</code>
                    </li>
                @endif
            </ul>
        </div>
        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('super-admin.diagnostics.run-migrations') }}">
                @csrf
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-database"></i>
                    Run Migrations Now
                </button>
            </form>
            <a href="{{ route('super-admin.diagnostics') }}"
               class="text-sm text-gray-400 hover:text-white transition-colors">
                Go to Diagnostics
            </a>
        </div>
    </div>
</div>
@endsection
