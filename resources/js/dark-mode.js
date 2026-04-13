/**
 * Dark Mode Manager
 * Manages theme preference using Tailwind CSS's dark mode class approach
 * Stores preference in localStorage and applies class to document root
 *
 * Layout opt-out: Add data-theme-mode="light" to <html> to force light mode
 * and prevent this manager from toggling dark mode on that page.
 */

export const DarkModeManager = {
    storageKey: 'theme-preference',
    darkClass: 'dark',
    _enabled: true,

    /**
     * Initialize dark mode manager
     * Should be called on app startup
     */
    init() {
        // Check if the current layout opts out of dark mode
        const forcedMode = document.documentElement.dataset.themeMode;
        if (forcedMode === 'light') {
            this._enabled = false;
            document.documentElement.classList.remove(this.darkClass);
            return;
        }

        // Get stored preference or detect system preference
        const preference = this.getPreference();
        this.applyTheme(preference);

        // Listen for system preference changes
        const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        darkModeQuery.addEventListener('change', () => {
            // Only auto-switch if user hasn't explicitly set a preference
            if (!localStorage.getItem(this.storageKey)) {
                this.applyTheme(darkModeQuery.matches ? 'dark' : 'light');
            }
        });
    },

    /**
     * Check if dark mode management is enabled on this page
     */
    isEnabled() {
        return this._enabled;
    },

    /**
     * Get current theme preference
     * Returns: 'dark' | 'light'
     */
    getPreference() {
        const stored = localStorage.getItem(this.storageKey);

        // If user has set a preference, use it
        if (stored === 'dark' || stored === 'light') {
            return stored;
        }

        // Otherwise, detect system preference
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return systemDark ? 'dark' : 'light';
    },

    /**
     * Check if dark mode is currently active
     */
    isDark() {
        return document.documentElement.classList.contains(this.darkClass);
    },

    /**
     * Apply theme to document
     * @param {string} theme - 'dark' or 'light'
     */
    applyTheme(theme) {
        if (!this._enabled) {
            return;
        }

        const isDark = theme === 'dark';

        if (isDark) {
            document.documentElement.classList.add(this.darkClass);
        } else {
            document.documentElement.classList.remove(this.darkClass);
        }

        // Persist choice
        localStorage.setItem(this.storageKey, theme);

        // Dispatch event for other parts of app
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
    },

    /**
     * Toggle between light and dark mode
     */
    toggle() {
        if (!this._enabled) {
            return this.isDark() ? 'dark' : 'light';
        }

        const newTheme = this.isDark() ? 'light' : 'dark';
        this.applyTheme(newTheme);
        return newTheme;
    },

    /**
     * Set theme to dark
     */
    setDark() {
        this.applyTheme('dark');
    },

    /**
     * Set theme to light
     */
    setLight() {
        this.applyTheme('light');
    },
};

export default DarkModeManager;
