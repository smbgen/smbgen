@props([
    'class' => 'ml-auto inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200 text-xs font-medium',
    'iconClass' => 'w-4 h-4',
])

<button
    x-data="{
        darkMode: false,
        init() {
            // Initialize with current dark mode state
            this.darkMode = window.DarkModeManager?.isDark() ?? document.documentElement.classList.contains('dark');

            // Listen for theme changes from other components
            window.addEventListener('theme-changed', (e) => {
                this.darkMode = e.detail.theme === 'dark';
            });
        },
        toggle() {
            const newTheme = window.DarkModeManager?.toggle() || (this.darkMode ? 'light' : 'dark');
            this.darkMode = newTheme === 'dark';
        }
    }"
    x-init="init()"
    @click="toggle()"
    :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
    :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
    class="{{ $class }}"
    {{ $attributes }}
>
    <!-- Sun Icon (shown in dark mode → click to go light) -->
    <svg x-cloak x-show="darkMode" class="{{ $iconClass }}" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm5.657-9.193a1 1 0 00-1.414 0l-.707.707A1 1 0 005.05 6.464l.707-.707a1 1 0 011.414-1.414zM5 8a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd" />
    </svg>

    <!-- Moon Icon (shown in light mode → click to go dark) -->
    <svg x-cloak x-show="!darkMode" class="{{ $iconClass }}" fill="currentColor" viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
    </svg>

    <span x-cloak x-show="darkMode" class="hidden sm:inline">Light</span>
    <span x-cloak x-show="!darkMode" class="hidden sm:inline">Dark</span>
</button>
