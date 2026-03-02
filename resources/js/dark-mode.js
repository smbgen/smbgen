/**
 * Dark Mode Manager
 * Manages theme preference using Tailwind CSS's dark mode class approach
 * Stores preference in localStorage and applies class to document root
 */

export const DarkModeManager = {
    storageKey: 'theme-preference',
    darkClass: 'dark',

    /**
     * Initialize dark mode manager
     * Should be called on app startup
     */
    init() {
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
        
        console.log('Dark mode initialized. Preference:', preference);
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
        const isDark = theme === 'dark';
        
        if (isDark) {
            document.documentElement.classList.add(this.darkClass);
        } else {
            document.documentElement.classList.remove(this.darkClass);
        }
        
        console.log('Applied theme:', theme, 'isDark:', isDark, 'classList:', document.documentElement.className);
        
        // Persist choice
        localStorage.setItem(this.storageKey, theme);
        
        // Dispatch event for other parts of app
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
    },

    /**
     * Toggle between light and dark mode
     */
    toggle() {
        const newTheme = this.isDark() ? 'light' : 'dark';
        console.log('Toggling theme from', this.isDark() ? 'dark' : 'light', 'to', newTheme);
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
