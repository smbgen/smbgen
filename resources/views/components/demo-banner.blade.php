@if(config('app.demo_mode'))
<div class="demo-banner relative z-50 flex items-center justify-center gap-3 bg-amber-400 dark:bg-amber-500 px-4 py-2 text-sm font-medium text-amber-900 dark:text-amber-950" role="banner" aria-label="Demo mode notice">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>You are in <strong>demo mode</strong>. Changes are reset every hour.</span>
    <a href="{{ route('demo.landing') }}" class="underline underline-offset-2 hover:no-underline transition-all">
        Return to Demo
    </a>
    <form method="POST" action="{{ route('logout') }}" class="ml-2">
        @csrf
        <button type="submit" class="underline underline-offset-2 hover:no-underline transition-all">
            Exit Demo
        </button>
    </form>
</div>
@endif
