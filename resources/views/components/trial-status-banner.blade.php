@php
    $trialEndsAt = \App\Models\BusinessSetting::get('trial_ends_at');
    $subscriptionPlan = \App\Models\BusinessSetting::get('subscription_plan');

    $showBanner = $trialEndsAt && !$subscriptionPlan && \Carbon\Carbon::parse($trialEndsAt)->isFuture();

    if ($showBanner) {
        $daysLeft = (int) now()->diffInDays(\Carbon\Carbon::parse($trialEndsAt), false);
        if ($daysLeft > 7) {
            $colorClasses = 'bg-green-500/10 border-green-500/30 text-green-300';
            $iconClass = 'fa-clock';
        } elseif ($daysLeft >= 3) {
            $colorClasses = 'bg-yellow-500/10 border-yellow-500/30 text-yellow-300';
            $iconClass = 'fa-exclamation-triangle';
        } else {
            $colorClasses = 'bg-red-500/10 border-red-500/30 text-red-300';
            $iconClass = 'fa-exclamation-circle';
        }
    }
@endphp

@if ($showBanner ?? false)
    <div x-data="{ dismissed: localStorage.getItem('trial_banner_dismissed') === '{{ $trialEndsAt }}' }"
         x-show="!dismissed"
         class="mt-2 p-2 rounded-lg border {{ $colorClasses }} text-xs">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <i class="fas {{ $iconClass }} text-sm"></i>
                <span>
                    Your free trial ends in <strong>{{ $daysLeft }} {{ Str::plural('day', $daysLeft) }}</strong>.
                    <a href="{{ route('admin.subscription.index') }}" class="underline ml-1">Upgrade now</a>
                </span>
            </div>
            <button @click="dismissed = true; localStorage.setItem('trial_banner_dismissed', '{{ $trialEndsAt }}')"
                    class="opacity-60 hover:opacity-100 transition-opacity shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>
@endif
