import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { DarkModeManager } from './dark-mode';

Alpine.plugin(collapse);

window.Alpine = Alpine;

// Make DarkModeManager globally accessible
window.DarkModeManager = DarkModeManager;

// Initialize dark mode manager
DarkModeManager.init();

// Prevent duplicate initialization (HMR, multiple loads, etc.)
if (!window.Alpine.__started) {
    Alpine.start();
    window.Alpine.__started = true;
}
