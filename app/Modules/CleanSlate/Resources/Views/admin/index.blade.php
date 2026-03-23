@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Extreme — Admin</h1>
        <p class="text-gray-500 text-sm mt-1">Subscriber overview</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Total Subscribers', 'value' => $stats['total_subscribers']],
            ['label' => 'Total Generations', 'value' => $stats['total_generations']],
            ['label' => 'This Month', 'value' => $stats['generations_this_month']],
            ['label' => 'Active Subscriptions', 'value' => $stats['active_subscriptions']],
        ] as $stat)
        <div class="p-4 rounded-xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Customer table --}}
    <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr>
                    <th class="text-left px-4 py-3 text-gray-500 font-medium">User</th>
                    <th class="text-left px-4 py-3 text-gray-500 font-medium">Plan</th>
                    <th class="text-left px-4 py-3 text-gray-500 font-medium">Generations</th>
                    <th class="text-left px-4 py-3 text-gray-500 font-medium">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($subscribers as $subscriber)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $subscriber->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $subscriber->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        {{ $subscriber->extreme_tier ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                        {{ $subscriber->generations_count }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $subscriber->created_at->format('M j, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No subscribers yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $subscribers->links() }}
</div>
@endsection
