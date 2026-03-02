@props([])

<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
        <div class="bg-gradient-to-r from-orange-600 dark:from-orange-500 to-red-600 dark:to-red-500 rounded-xl p-2">
            <i class="fas fa-bug text-white"></i>
        </div>
        Debug Tools
    </h3>
    
    <div class="space-y-3">
        <!-- Info Page -->
        @if(Route::has('debug.info'))
        <a href="{{ route('debug.info') }}" target="_blank" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 dark:bg-blue-500/20 rounded-lg p-2 group-hover:bg-blue-200 dark:group-hover:bg-blue-500/30 transition-colors">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">System Info</div>
                    <div class="text-gray-600 dark:text-gray-400 text-xs">Environment & configuration</div>
                </div>
            </div>
            <i class="fas fa-external-link-alt text-gray-400 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors"></i>
        </a>
        @endif

        <!-- Design System -->
        @if(Route::has('debug.design'))
        <a href="{{ route('debug.design') }}" target="_blank" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="bg-purple-100 dark:bg-purple-500/20 rounded-lg p-2 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-colors">
                    <i class="fas fa-palette text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">Design System</div>
                    <div class="text-gray-600 dark:text-gray-400 text-xs">UI components & styles</div>
                </div>
            </div>
            <i class="fas fa-external-link-alt text-gray-400 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors"></i>
        </a>
        @endif

        <!-- Error Pages -->
        <div class="grid grid-cols-2 gap-2">
            @if(Route::has('debug.test.403'))
            <a href="{{ route('debug.test.403') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-red-100 dark:bg-red-500/10 hover:bg-red-200 dark:hover:bg-red-500/20 rounded-lg transition-colors text-red-600 dark:text-red-400 text-xs font-medium">
                <i class="fas fa-ban"></i> 403
            </a>
            @endif
            
            @if(Route::has('debug.test.404'))
            <a href="{{ route('debug.test.404') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-orange-100 dark:bg-orange-500/10 hover:bg-orange-200 dark:hover:bg-orange-500/20 rounded-lg transition-colors text-orange-600 dark:text-orange-400 text-xs font-medium">
                <i class="fas fa-question-circle"></i> 404
            </a>
            @endif
            
            @if(Route::has('debug.test.405'))
            <a href="{{ route('debug.test.405') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-yellow-100 dark:bg-yellow-500/10 hover:bg-yellow-200 dark:hover:bg-yellow-500/20 rounded-lg transition-colors text-yellow-600 dark:text-yellow-400 text-xs font-medium">
                <i class="fas fa-minus-circle"></i> 405
            </a>
            @endif
            
            @if(Route::has('debug.test.500'))
            <a href="{{ route('debug.test.500') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-red-100 dark:bg-red-500/10 hover:bg-red-200 dark:hover:bg-red-500/20 rounded-lg transition-colors text-red-600 dark:text-red-400 text-xs font-medium">
                <i class="fas fa-exclamation-triangle"></i> 500
            </a>
            @endif
            
            @if(Route::has('debug.test.503'))
            <a href="{{ route('debug.test.503') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-purple-100 dark:bg-purple-500/10 hover:bg-purple-200 dark:hover:bg-purple-500/20 rounded-lg transition-colors text-purple-600 dark:text-purple-400 text-xs font-medium">
                <i class="fas fa-server"></i> 503
            </a>
            @endif
        </div>

        <!-- Test Routes -->
        @if(Route::has('payment.test'))
        <a href="{{ route('payment.test') }}" target="_blank" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all group">
            <div class="flex items-center gap-3">
                <div class="bg-cyan-100 dark:bg-cyan-500/20 rounded-lg p-2 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-500/30 transition-colors">
                    <i class="fas fa-credit-card text-cyan-600 dark:text-cyan-400"></i>
                </div>
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">Payment Test</div>
                    <div class="text-gray-600 dark:text-gray-400 text-xs">Test payment flow</div>
                </div>
            </div>
            <i class="fas fa-external-link-alt text-gray-400 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors"></i>
        </a>
        @endif
    </div>

    <!-- Quick Info -->
    <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-2">
                <div class="text-gray-600 dark:text-gray-400 mb-1">Environment</div>
                <div class="text-gray-900 dark:text-white font-bold">{{ app()->environment() }}</div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700/30 rounded-lg p-2">
                <div class="text-gray-600 dark:text-gray-400 mb-1">Debug Mode</div>
                <div class="text-gray-900 dark:text-white font-bold">{{ config('app.debug') ? 'ON' : 'OFF' }}</div>
            </div>
        </div>
    </div>
</div>
