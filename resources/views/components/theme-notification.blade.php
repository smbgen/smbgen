@props([
    'autoDismissAfter' => 10000, // 10 seconds in milliseconds
])

<div
    x-data="{
        show: false,
        isSystemPreference: false,
        init() {
            // Check if this is first load with system preference
            const stored = localStorage.getItem('theme-preference');
            this.isSystemPreference = !stored;
            
            if (this.isSystemPreference) {
                this.show = true;
                setTimeout(() => {
                    this.show = false;
                }, {{ $autoDismissAfter }});
            }
        }
    }"
    x-init="init()"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed bottom-4 right-4 max-w-sm z-50"
    style="display: none;"
>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-start gap-4 p-4">
            <!-- Icon -->
            <div class="flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.077 13.308-5.077 18.384 0a1 1 0 01-1.414 1.414zM14.95 11.05a7 7 0 00-9.9 0 1 1 0 01-1.414-1.414 9 9 0 0112.728 0 1 1 0 01-1.414 1.414zM12.12 13.88a3 3 0 00-4.242 0 1 1 0 01-1.415-1.415 5 5 0 017.072 0 1 1 0 01-1.415 1.415zM9 16a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                    Theme Preference Detected
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    This app is inheriting your system mode: <span class="font-medium" x-text="document.documentElement.classList.contains('dark') ? '🌙 Dark' : '☀️ Light'"></span>
                </p>
            </div>

            <!-- Close Button -->
            <button
                @click="show = false"
                class="flex-shrink-0 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors"
                aria-label="Dismiss notification"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div
            class="h-1 bg-blue-600 dark:bg-blue-500 origin-left"
            x-data
            x-init="
                const duration = {{ $autoDismissAfter }};
                const startTime = Date.now();
                const animate = () => {
                    if (!$el.parentElement.parentElement) return;
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    $el.style.transform = `scaleX(${1 - progress})`;
                    if (progress < 1) requestAnimationFrame(animate);
                };
                animate();
            "
            style="transform: scaleX(1);"
        ></div>
    </div>
</div>
