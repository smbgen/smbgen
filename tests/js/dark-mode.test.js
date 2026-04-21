import { describe, it, expect, beforeEach, vi } from 'vitest';
import { DarkModeManager } from '../../resources/js/dark-mode';

// --- helpers ----------------------------------------------------------------

function mockMatchMedia(prefersDark = false) {
    const mock = {
        matches: prefersDark,
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
    };

    window.matchMedia = vi.fn().mockReturnValue(mock);

    return mock;
}

// --- suite ------------------------------------------------------------------

describe('DarkModeManager', () => {
    beforeEach(() => {
        // Reset DOM and localStorage before each test
        document.documentElement.classList.remove('dark');
        document.documentElement.removeAttribute('data-theme-mode');
        localStorage.clear();
        DarkModeManager._enabled = true;
        mockMatchMedia(false);
    });

    describe('init()', () => {
        it('removes dark class when layout forces light mode', () => {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme-mode', 'light');

            DarkModeManager.init();

            expect(document.documentElement.classList.contains('dark')).toBe(false);
            expect(DarkModeManager.isEnabled()).toBe(false);
        });

        it('applies stored dark preference on init', () => {
            localStorage.setItem('theme-preference', 'dark');
            mockMatchMedia(false);

            DarkModeManager._enabled = true;
            DarkModeManager.init();

            expect(document.documentElement.classList.contains('dark')).toBe(true);
        });

        it('applies stored light preference on init', () => {
            localStorage.setItem('theme-preference', 'light');
            document.documentElement.classList.add('dark');

            DarkModeManager._enabled = true;
            DarkModeManager.init();

            expect(document.documentElement.classList.contains('dark')).toBe(false);
        });

        it('follows system dark preference when no stored preference', () => {
            mockMatchMedia(true);

            DarkModeManager._enabled = true;
            DarkModeManager.init();

            expect(document.documentElement.classList.contains('dark')).toBe(true);
        });

        it('follows system light preference when no stored preference', () => {
            mockMatchMedia(false);

            DarkModeManager._enabled = true;
            DarkModeManager.init();

            expect(document.documentElement.classList.contains('dark')).toBe(false);
        });
    });

    describe('getPreference()', () => {
        it('returns stored dark preference', () => {
            localStorage.setItem('theme-preference', 'dark');
            expect(DarkModeManager.getPreference()).toBe('dark');
        });

        it('returns stored light preference', () => {
            localStorage.setItem('theme-preference', 'light');
            expect(DarkModeManager.getPreference()).toBe('light');
        });

        it('returns dark from system when not stored and system prefers dark', () => {
            mockMatchMedia(true);
            expect(DarkModeManager.getPreference()).toBe('dark');
        });

        it('returns light from system when not stored and system prefers light', () => {
            mockMatchMedia(false);
            expect(DarkModeManager.getPreference()).toBe('light');
        });

        it('ignores invalid stored values and falls back to system', () => {
            localStorage.setItem('theme-preference', 'invalid-value');
            mockMatchMedia(true);
            expect(DarkModeManager.getPreference()).toBe('dark');
        });
    });

    describe('isDark()', () => {
        it('returns true when dark class is present', () => {
            document.documentElement.classList.add('dark');
            expect(DarkModeManager.isDark()).toBe(true);
        });

        it('returns false when dark class is absent', () => {
            document.documentElement.classList.remove('dark');
            expect(DarkModeManager.isDark()).toBe(false);
        });
    });

    describe('applyTheme()', () => {
        it('adds dark class for dark theme', () => {
            DarkModeManager.applyTheme('dark');
            expect(document.documentElement.classList.contains('dark')).toBe(true);
        });

        it('removes dark class for light theme', () => {
            document.documentElement.classList.add('dark');
            DarkModeManager.applyTheme('light');
            expect(document.documentElement.classList.contains('dark')).toBe(false);
        });

        it('persists preference in localStorage', () => {
            DarkModeManager.applyTheme('dark');
            expect(localStorage.getItem('theme-preference')).toBe('dark');

            DarkModeManager.applyTheme('light');
            expect(localStorage.getItem('theme-preference')).toBe('light');
        });

        it('dispatches theme-changed event', () => {
            const listener = vi.fn();
            window.addEventListener('theme-changed', listener);

            DarkModeManager.applyTheme('dark');

            expect(listener).toHaveBeenCalledOnce();
            expect(listener.mock.calls[0][0].detail).toEqual({ theme: 'dark' });

            window.removeEventListener('theme-changed', listener);
        });

        it('does nothing when manager is disabled', () => {
            DarkModeManager._enabled = false;
            DarkModeManager.applyTheme('dark');
            expect(document.documentElement.classList.contains('dark')).toBe(false);
        });
    });

    describe('toggle()', () => {
        it('switches from light to dark', () => {
            document.documentElement.classList.remove('dark');
            const result = DarkModeManager.toggle();
            expect(result).toBe('dark');
            expect(document.documentElement.classList.contains('dark')).toBe(true);
        });

        it('switches from dark to light', () => {
            document.documentElement.classList.add('dark');
            const result = DarkModeManager.toggle();
            expect(result).toBe('light');
            expect(document.documentElement.classList.contains('dark')).toBe(false);
        });

        it('returns current theme without toggling when disabled', () => {
            DarkModeManager._enabled = false;
            document.documentElement.classList.add('dark');
            const result = DarkModeManager.toggle();
            expect(result).toBe('dark');
            expect(document.documentElement.classList.contains('dark')).toBe(true);
        });
    });

    describe('setDark() / setLight()', () => {
        it('setDark() applies dark theme', () => {
            DarkModeManager.setDark();
            expect(DarkModeManager.isDark()).toBe(true);
        });

        it('setLight() applies light theme', () => {
            document.documentElement.classList.add('dark');
            DarkModeManager.setLight();
            expect(DarkModeManager.isDark()).toBe(false);
        });
    });
});
