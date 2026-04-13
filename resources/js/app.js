import '../css/app.css';
import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { DarkModeManager } from './dark-mode';

function initFrontendMotion() {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const frontendMain = document.querySelector('.fe-root main');

    if (!frontendMain) {
        return;
    }

    const explicitTargets = frontendMain.querySelectorAll('[data-reveal]');
    const sectionTargets = frontendMain.querySelectorAll('section > div:first-child');
    const staggerGroups = frontendMain.querySelectorAll('[data-reveal-stagger]');

    const revealTargets = new Set([...explicitTargets, ...sectionTargets]);

    staggerGroups.forEach((group) => {
        [...group.children].forEach((child, index) => {
            child.classList.add('fe-reveal');
            child.style.setProperty('--reveal-delay', `${Math.min(index, 7) * 70}ms`);
            revealTargets.add(child);
        });
    });

    if (reducedMotion) {
        revealTargets.forEach((target) => target.classList.add('is-visible'));
        return;
    }

    revealTargets.forEach((target) => target.classList.add('fe-reveal'));

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        },
        {
            root: null,
            threshold: 0.14,
            rootMargin: '0px 0px -8% 0px',
        },
    );

    revealTargets.forEach((target) => observer.observe(target));
}

function initMagneticElements() {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reducedMotion) {
        return;
    }

    document.querySelectorAll('[data-magnetic]').forEach((element) => {
        element.addEventListener('mousemove', (event) => {
            const rect = element.getBoundingClientRect();
            const offsetX = (event.clientX - rect.left) / rect.width - 0.5;
            const offsetY = (event.clientY - rect.top) / rect.height - 0.5;
            element.style.transform = `translate(${offsetX * 10}px, ${offsetY * 8}px)`;
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translate(0, 0)';
        });
    });
}

// If Livewire (or another script) already started Alpine, use that instance
// for plugin registration so we don't start a second Alpine instance.
// Livewire's @livewireScripts is a blocking (non-deferred) script at the
// bottom of <body> and runs before this deferred module, meaning
// window.Alpine will already be set before we get here.
const alpine = window.Alpine ?? Alpine;

if (!alpine.__appPluginsLoaded) {
    alpine.plugin(collapse);
    alpine.__appPluginsLoaded = true;
}

// Make DarkModeManager globally accessible
window.DarkModeManager = DarkModeManager;

// Initialize dark mode manager
DarkModeManager.init();
initFrontendMotion();
initMagneticElements();

// Only start Alpine if no other script has already started it
if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}
