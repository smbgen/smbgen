# Dark Mode/Light Mode Implementation Guide

## Overview

The application now has a **unified, consistent dark mode/light mode system** that uses Tailwind CSS's built-in `darkMode: 'class'` configuration.

## How It Works

### Dark Mode Manager (`resources/js/dark-mode.js`)

The dark mode is managed by a JavaScript module that:
- Detects system preference on first load
- Stores user preference in `localStorage` with the key `theme-preference`
- Applies or removes the `dark` class to the `<html>` element
- Respects OS-level dark mode preference changes
- Dispatches a custom `theme-changed` event for other components to listen to

### Automatic Initialization

The dark mode manager is automatically initialized when the app loads via `resources/js/app.js`.

## Styling with Dark Mode

### Light Mode (Default)

```html
<div class="bg-white text-gray-900">
    This appears in light mode
</div>
```

### Dark Mode

Use the `dark:` prefix to define dark mode styles:

```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    This will switch appearance based on the theme
</div>
```

### Component Classes

All component classes (buttons, cards, forms, etc.) have been updated to support both light and dark modes:

**Examples:**

```html
<!-- Card that works in both modes -->
<div class="card">
    Content here
</div>

<!-- Button that responds to theme -->
<button class="btn-primary">
    Click me
</button>

<!-- Form input that adapts -->
<input class="form-input">

<!-- Badge that changes with theme -->
<span class="badge-success">Success</span>
```

## Adding Dark Mode Toggle

You can add a dark mode toggle button anywhere in your templates:

```blade
<x-dark-mode-toggle />
```

Or with custom styling:

```blade
<x-dark-mode-toggle 
    class="custom-class"
    iconClass="w-6 h-6"
/>
```

The toggle will:
- Show a sun icon in dark mode (clicking switches to light)
- Show a moon icon in light mode (clicking switches to dark)
- Persist the user's choice in localStorage
- Work with the dark mode manager automatically

## How to Style Components

### DO: Use `dark:` prefix

```blade
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    This is correct
</div>
```

### DON'T: Hardcode dark colors

```blade
<!-- ❌ Don't do this -->
<div class="bg-gray-800 text-gray-100">
    Hardcoded dark colors - won't switch!
</div>
```

### Use Semantic Color Variables

The CSS uses semantic color names that adapt to the theme:
- `.card` - Card container
- `.widget-card` - Dashboard widget
- `.stat-card` - Statistics card
- `.btn-primary`, `.btn-secondary`, etc. - Buttons
- `.form-input`, `.form-select`, `.form-textarea` - Form elements
- `.badge-success`, `.badge-error`, etc. - Badges
- `.alert`, `.alert-success`, `.alert-error` - Alerts

## Programmatic Control

You can interact with the dark mode manager in JavaScript:

```javascript
import { DarkModeManager } from './dark-mode';

// Get current preference
const isDark = DarkModeManager.isDark();

// Set theme
DarkModeManager.setDark();
DarkModeManager.setLight();

// Toggle theme
const newTheme = DarkModeManager.toggle();

// Listen for theme changes
window.addEventListener('theme-changed', (event) => {
    console.log('Theme changed to:', event.detail.theme);
});
```

## CSS File Changes

The `resources/css/app.css` has been completely refactored:

**Before (Problems):**
- Mixed `data-theme="dark"` CSS variables
- Hardcoded dark colors in components
- Inconsistent use of Tailwind's `dark:` prefix
- No actual dark mode toggle

**After (Fixed):**
- All components use Tailwind's `dark:` prefix
- Light mode is the default with bright colors
- Dark mode is applied with bright text on dark backgrounds
- Consistent styling across the entire application
- Proper transitions between modes

## Testing Dark Mode

1. Open the app in your browser
2. Check your system preference (light/dark mode)
3. The app should automatically match your system preference
4. Add the dark mode toggle: `<x-dark-mode-toggle />`
5. Click the toggle to switch between light and dark
6. Refresh the page - your choice should persist

## Common Issues & Solutions

### Components not switching color
- Make sure you're using `dark:` classes, not hardcoded colors
- Check that the `dark` class is actually on the `<html>` element
- Open browser DevTools and check the element inspector

### CSS not loading in dark mode
- Run `npm run build` to rebuild assets
- Clear your browser cache (Ctrl+Shift+Delete)
- Make sure you're using Tailwind's `dark:` prefix correctly

### Dark mode toggle not appearing
- Make sure `<x-dark-mode-toggle />` is placed correctly
- Check that Alpine.js is loaded (it's required for the toggle)
- Clear browser cache and rebuild

## Files Changed

- `resources/js/dark-mode.js` - New dark mode manager
- `resources/js/app.js` - Updated to initialize dark mode
- `resources/css/app.css` - Completely refactored for Tailwind dark mode
- `resources/views/layouts/app.blade.php` - Updated for light/dark styles
- `resources/views/layouts/guest.blade.php` - Updated for light/dark styles
- `resources/views/components/dark-mode-toggle.blade.php` - New toggle component

## Next Steps

1. Run `npm run dev` for development with auto-rebuild
2. Add the dark mode toggle to your main navigation or settings
3. Audit your views and ensure they use Tailwind's `dark:` prefix
4. Test thoroughly in both light and dark modes
5. Update any custom CSS to follow the new pattern
