@php
    use Illuminate\Support\Facades\DB;
    
    // Get tenant info from current tenant context
    $tenant = null;
    $trialDaysRemaining = null;
    $showBanner = false;
    
    try {
        if (function_exists('tenant')) {
            $tenant = tenant();
            
            if ($tenant && $tenant->trial_ends_at) {
                $trialEndsAt = \Carbon\Carbon::parse($tenant->trial_ends_at);
                $now = now();
                
                // Only show if trial is active (not expired)
                if ($trialEndsAt->isFuture()) {
                    $trialDaysRemaining = $now->diffInDays($trialEndsAt, false);
                    
                    // Check if user dismissed the banner
                    $dismissed = \App\Models\BusinessSetting::get('trial_banner_dismissed', false);
                    $showBanner = !$dismissed;
                }
            }
        }
    } catch (\Exception $e) {
        // Silently fail if tenancy not initialized
    }
@endphp

@if($showBanner && $trialDaysRemaining !== null)
    @php
        // Color coding based on days remaining
        if ($trialDaysRemaining > 7) {
            $bgColor = 'bg-blue-50 dark:bg-blue-900/20';
            $borderColor = 'border-blue-200 dark:border-blue-800';
            $textColor = 'text-blue-800 dark:text-blue-200';
            $buttonColor = 'bg-blue-600 hover:bg-blue-700';
            $iconColor = 'text-blue-500';
        } elseif ($trialDaysRemaining >= 3) {
            $bgColor = 'bg-yellow-50 dark:bg-yellow-900/20';
            $borderColor = 'border-yellow-200 dark:border-yellow-800';
            $textColor = 'text-yellow-800 dark:text-yellow-200';
            $buttonColor = 'bg-yellow-600 hover:bg-yellow-700';
            $iconColor = 'text-yellow-500';
        } else {
            $bgColor = 'bg-red-50 dark:bg-red-900/20';
            $borderColor = 'border-red-200 dark:border-red-800';
            $textColor = 'text-red-800 dark:text-red-200';
            $buttonColor = 'bg-red-600 hover:bg-red-700';
            $iconColor = 'text-red-500';
        }
        
        $daysText = $trialDaysRemaining == 1 ? 'day' : 'days';
    @endphp

    <div class="{{ $bgColor }} {{ $borderColor }} border rounded-lg p-4 mb-6 shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="{{ $textColor }} font-semibold">
                        Trial Period: {{ $trialDaysRemaining }} {{ $daysText }} remaining
                    </p>
                    <p class="text-sm {{ $textColor }} opacity-90">
                        Upgrade now to continue using all features after your trial ends
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.subscription.plans') }}" 
                   class="{{ $buttonColor }} text-white px-4 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out hover:shadow-md">
                    Upgrade Now
                </a>
                
                <button @click="dismissBanner()" 
                        class="{{ $textColor }} hover:opacity-75 transition-opacity"
                        title="Dismiss">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function dismissBanner() {
            fetch('{{ route('admin.trial-banner.dismiss') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                // Alpine.js will handle hiding via x-show
                document.querySelector('[x-data]').setAttribute('x-show', 'false');
            });
        }
    </script>
@endif
