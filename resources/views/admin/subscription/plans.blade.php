@extends('layouts.admin')

@section('title', 'Subscription Plans')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Choose Your Plan</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Select the plan that best fits your business needs</p>
    </div>

    <!-- Current Plan Badge -->
    @if($tenant->plan)
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-blue-800 dark:text-blue-200 font-medium">
                    Current Plan: <span class="font-bold capitalize">{{ $tenant->plan }}</span>
                </span>
            </div>
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="grid md:grid-cols-3 gap-8">
        @foreach($plans as $planKey => $plan)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border-2 {{ $tenant->plan === $planKey ? 'border-blue-500' : 'border-gray-200 dark:border-gray-700' }} transition-transform hover:scale-105">
                <!-- Plan Header -->
                <div class="p-6 {{ $planKey === 'professional' ? 'bg-gradient-to-br from-blue-500 to-purple-600' : 'bg-gray-50 dark:bg-gray-900' }}">
                    <h3 class="text-2xl font-bold {{ $planKey === 'professional' ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                        {{ $plan['name'] }}
                    </h3>
                    <div class="mt-4">
                        <span class="text-4xl font-extrabold {{ $planKey === 'professional' ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                            {{ $plan['price'] }}
                        </span>
                        <span class="{{ $planKey === 'professional' ? 'text-blue-100' : 'text-gray-600 dark:text-gray-400' }}">
                            /{{ $plan['interval'] }}
                        </span>
                    </div>
                    @if($planKey === 'professional')
                        <span class="inline-block mt-2 bg-yellow-400 text-gray-900 text-xs font-semibold px-3 py-1 rounded-full">
                            MOST POPULAR
                        </span>
                    @endif
                </div>

                <!-- Features List -->
                <div class="p-6">
                    <ul class="space-y-4 mb-6">
                        @foreach($plan['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Action Button -->
                    @if($tenant->plan === $planKey)
                        <button disabled class="w-full bg-gray-300 text-gray-600 px-4 py-2 rounded-md font-medium cursor-not-allowed">
                            Current Plan
                        </button>
                    @else
                        <form action="{{ route('admin.subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $planKey }}">
                            <button type="submit" class="w-full {{ $planKey === 'professional' ? 'bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-md font-medium transition duration-150 ease-in-out">
                                @if($tenant->plan)
                                    {{ $planKey === 'enterprise' ? 'Upgrade' : 'Change' }} to {{ $plan['name'] }}
                                @else
                                    Get Started
                                @endif
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Additional Info -->
    <div class="mt-12 text-center">
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            All plans include 14-day free trial. No credit card required to start.
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-500">
            Need help choosing? <a href="mailto:support@clientbridge.app" class="text-blue-600 hover:text-blue-700 underline">Contact our team</a>
        </p>
    </div>

    @if($tenant->stripe_subscription_id)
        <div class="mt-8 text-center">
            <a href="{{ route('admin.subscription.manage') }}" class="text-blue-600 hover:text-blue-700 underline">
                Manage Your Subscription
            </a>
        </div>
    @endif
</div>
@endsection
