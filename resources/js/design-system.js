/**
 * SMBGen Design System
 * 
 * Unified design language for consistent UI/UX across the application.
 * Based on professional SaaS dashboard patterns with dark theme support.
 */

export const designSystem = {
    // Color Palette - Professional dark theme
    colors: {
        // Primary brand colors
        primary: {
            50: '#eff6ff',
            100: '#dbeafe',
            200: '#bfdbfe',
            300: '#93c5fd',
            400: '#60a5fa',
            500: '#3b82f6',  // Main primary
            600: '#2563eb',
            700: '#1d4ed8',
            800: '#1e40af',
            900: '#1e3a8a',
        },
        
        // Accent colors
        accent: {
            purple: '#8b5cf6',
            pink: '#ec4899',
            cyan: '#06b6d4',
            green: '#10b981',
            orange: '#f59e0b',
            red: '#ef4444',
        },
        
        // Neutral grays (dark theme optimized)
        gray: {
            50: '#f9fafb',
            100: '#f3f4f6',
            200: '#e5e7eb',
            300: '#d1d5db',
            400: '#9ca3af',
            500: '#6b7280',
            600: '#4b5563',
            700: '#374151',
            800: '#1f2937',  // Main background
            850: '#1a222e',  // Darker variant
            900: '#111827',  // Deepest
            950: '#0a0e16',  // Nearly black
        },
        
        // Status colors
        status: {
            success: '#10b981',
            warning: '#f59e0b',
            error: '#ef4444',
            info: '#3b82f6',
        },
    },
    
    // Spacing scale (Tailwind compatible)
    spacing: {
        xs: '0.5rem',    // 8px
        sm: '0.75rem',   // 12px
        md: '1rem',      // 16px
        lg: '1.5rem',    // 24px
        xl: '2rem',      // 32px
        '2xl': '3rem',   // 48px
        '3xl': '4rem',   // 64px
    },
    
    // Border radius
    radius: {
        sm: '0.375rem',   // 6px
        md: '0.5rem',     // 8px
        lg: '0.75rem',    // 12px
        xl: '1rem',       // 16px
        '2xl': '1.5rem',  // 24px
        full: '9999px',
    },
    
    // Shadows (optimized for dark backgrounds)
    shadows: {
        sm: '0 1px 2px 0 rgba(0, 0, 0, 0.3)',
        md: '0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3)',
        lg: '0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3)',
        xl: '0 20px 25px -5px rgba(0, 0, 0, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.3)',
        glow: '0 0 20px rgba(59, 130, 246, 0.3)',
    },
    
    // Typography
    typography: {
        fontFamily: {
            sans: "'Figtree', 'Inter', system-ui, -apple-system, sans-serif",
            mono: "'JetBrains Mono', 'Fira Code', monospace",
        },
        fontSize: {
            xs: '0.75rem',     // 12px
            sm: '0.875rem',    // 14px
            base: '1rem',      // 16px
            lg: '1.125rem',    // 18px
            xl: '1.25rem',     // 20px
            '2xl': '1.5rem',   // 24px
            '3xl': '1.875rem', // 30px
            '4xl': '2.25rem',  // 36px
        },
        fontWeight: {
            normal: 400,
            medium: 500,
            semibold: 600,
            bold: 700,
        },
    },
    
    // Component patterns
    components: {
        // Card/Widget standard
        card: {
            base: 'bg-gray-800/60 backdrop-blur-sm rounded-xl border border-gray-700/50 shadow-lg',
            hover: 'hover:border-gray-600/50 hover:shadow-xl transition-all duration-200',
            padding: 'p-6',
        },
        
        // Button variants
        button: {
            primary: 'bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg',
            secondary: 'bg-gray-700 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200',
            danger: 'bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200',
            ghost: 'bg-transparent hover:bg-gray-700/50 text-gray-300 hover:text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200',
        },
        
        // Input fields
        input: {
            base: 'bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 text-gray-100 placeholder-gray-500 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors duration-200',
        },
        
        // Stat cards
        stat: {
            base: 'bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6 shadow-lg hover:shadow-xl hover:border-gray-600/50 transition-all duration-200',
            icon: 'w-12 h-12 rounded-lg flex items-center justify-center text-white shadow-md',
        },
        
        // Badges
        badge: {
            success: 'bg-green-500/20 text-green-400 border border-green-500/30 px-3 py-1 rounded-full text-xs font-medium',
            warning: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-3 py-1 rounded-full text-xs font-medium',
            error: 'bg-red-500/20 text-red-400 border border-red-500/30 px-3 py-1 rounded-full text-xs font-medium',
            info: 'bg-blue-500/20 text-blue-400 border border-blue-500/30 px-3 py-1 rounded-full text-xs font-medium',
            neutral: 'bg-gray-500/20 text-gray-400 border border-gray-500/30 px-3 py-1 rounded-full text-xs font-medium',
        },
    },
    
    // Layout patterns
    layout: {
        // Sidebar navigation
        sidebar: {
            width: '280px',
            collapsedWidth: '80px',
        },
        
        // Content spacing
        container: 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
        section: 'mb-8',
        
        // Grid patterns
        grid: {
            stats: 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6',
            twoCol: 'grid grid-cols-1 lg:grid-cols-2 gap-6',
            threeCol: 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6',
        },
    },
    
    // Animation/Transition standards
    transitions: {
        fast: '150ms',
        base: '200ms',
        slow: '300ms',
        
        ease: 'cubic-bezier(0.4, 0, 0.2, 1)',
        easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
        easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
        easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)',
    },
};

// Export utility classes for Blade components
export const classes = {
    // Quick access to common class strings
    card: `${designSystem.components.card.base} ${designSystem.components.card.padding}`,
    cardHover: `${designSystem.components.card.base} ${designSystem.components.card.hover} ${designSystem.components.card.padding}`,
    button: designSystem.components.button.primary,
    input: designSystem.components.input.base,
    container: designSystem.layout.container,
    section: designSystem.layout.section,
};

// Make available globally for Blade templates
if (typeof window !== 'undefined') {
    window.designSystem = designSystem;
    window.dsClasses = classes;
}

export default designSystem;
