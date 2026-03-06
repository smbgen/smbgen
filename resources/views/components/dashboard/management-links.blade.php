@props(['links'])

<!-- Management Quick Links Widget -->
<div class="bg-gradient-to-br from-indigo-600 dark:from-indigo-600 to-purple-600 dark:to-purple-600 rounded-2xl p-6 shadow-xl">
    <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
        <i class="fas fa-link text-white"></i>
        Quick Links
    </h3>
    
    <div class="space-y-2">
        <a href="{{ route('clients.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-users"></i>
                All Clients
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        
        @if(config('business.features.booking'))
        <a href="{{ route('admin.bookings.dashboard') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-calendar-check"></i>
                Recent Bookings
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        @endif
        
        <a href="{{ route('admin.leads.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-inbox"></i>
                All Leads
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        
        @if(\Route::has('admin.email-logs.index'))
        <a href="{{ route('admin.email-logs.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-chart-line"></i>
                Email Logs
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        @endif
        
        @if(\Route::has('admin.activity-logs.index'))
        <a href="{{ route('admin.activity-logs.index') }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-history"></i>
                Activity Logs
            </span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        @endif
        
        @foreach($links as $link)
        <a href="{{ route($link['route']) }}" 
           class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors group backdrop-blur-sm">
            <span class="text-white font-medium">{{ $link['title'] }}</span>
            <i class="fas fa-arrow-right text-white/70 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
        </a>
        @endforeach
    </div>
</div>
