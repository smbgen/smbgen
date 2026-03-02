@extends('layouts.admin')

@section('title', 'Manage Subscription')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Subscription</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">View and manage your billing information</p>
    </div>

    <!-- Current Plan Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Current Plan</h2>
            @if($subscription->plan)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ ucfirst($subscription->plan) }}
                </span>
            @endif
        </div>

        @if($stripeSubscription)
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white capitalize">
                        {{ $stripeSubscription->status }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Billing Cycle</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ ucfirst($stripeSubscription->items->data[0]->price->recurring->interval ?? 'monthly') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Period</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ date('M d, Y', $stripeSubscription->current_period_start) }} - {{ date('M d, Y', $stripeSubscription->current_period_end) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Next Payment</p>
                    <p class="text-lg font-medium text-gray-900 dark:text-white">
                        @if($stripeSubscription->cancel_at_period_end)
                            <span class="text-red-600">Cancels on {{ date('M d, Y', $stripeSubscription->current_period_end) }}</span>
                        @else
                            {{ date('M d, Y', $stripeSubscription->current_period_end) }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-wrap gap-4">
                <a href="{{ route('admin.subscription.plans') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-150 ease-in-out">
                    Change Plan
                </a>

                <a href="{{ route('admin.subscription.portal') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-md transition duration-150 ease-in-out">
                    Billing Portal
                </a>

                @if($stripeSubscription->cancel_at_period_end)
                    <form action="{{ route('admin.subscription.reactivate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition duration-150 ease-in-out">
                            Reactivate Subscription
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.subscription.cancel') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel your subscription? You will retain access until the end of your billing period.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150 ease-in-out">
                            Cancel Subscription
                        </button>
                    </form>
                @endif
            </div>

            @if($stripeSubscription->cancel_at_period_end)
                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Notice:</strong> Your subscription will be cancelled on {{ date('M d, Y', $stripeSubscription->current_period_end) }}. You can reactivate anytime before then.
                    </p>
                </div>
            @endif
        @elseif($subscription->trial_ends_at && now()->lt($subscription->trial_ends_at))
            <!-- Trial Period -->
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Trial Period Active</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Your trial ends on {{ \Carbon\Carbon::parse($subscription->trial_ends_at)->format('M d, Y') }}
                    ({{ now()->diffInDays($subscription->trial_ends_at) }} days remaining)
                </p>
                <a href="{{ route('admin.subscription.plans') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-150 ease-in-out">
                    Choose a Plan
                </a>
            </div>
        @else
            <!-- No Active Subscription -->
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No Active Subscription</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Subscribe to a plan to continue using smbgen
                </p>
                <a href="{{ route('admin.subscription.plans') }}" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-150 ease-in-out">
                    View Plans
                </a>
            </div>
        @endif
    </div>

    <!-- Billing History -->
    @if($stripeSubscription)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Billing History</h2>
            <p class="text-gray-600 dark:text-gray-400">
                View your complete billing history and download invoices in the 
                <a href="{{ route('admin.subscription.portal') }}" class="text-blue-600 hover:text-blue-700 underline">Billing Portal</a>.
            </p>
        </div>
    @endif
</div>
@endsection
