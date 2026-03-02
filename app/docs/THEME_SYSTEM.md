# Theme System Documentation

## Overview

ClientBridge includes a flexible CSS variable-based theme system that allows you to switch between light and dark modes globally without code changes.

## Configuration

### Setting the Theme Mode

Add to your `.env` file:

```env
THEME_MODE=dark
```

**Available options:**
- `dark` (default) - Dark mode for the entire admin panel
- `light` - Light mode for the entire admin panel

### Configuration File

The theme is configured in `config/business.php`:

```php
'theme' => [
    'mode' => env('THEME_MODE', 'dark'), // 'light' or 'dark'
],
```

## How It Works

### 1. Data Attribute

The theme mode is set on the `<body>` tag via a `data-theme` attribute:

```blade
<body data-theme="{{ config('business.theme.mode') }}">
```

### 2. CSS Variables

Theme colors are defined using CSS variables in `resources/css/app.css`:

```css
:root {
    /* Light mode colors (default) */
    --bg-primary: #ffffff;
    --bg-secondary: #f9fafb;
    --bg-tertiary: #f3f4f6;
    --text-primary: #111827;
    --text-secondary: #6b7280;
    --text-tertiary: #9ca3af;
    --border-color: #e5e7eb;
    --hover-bg: #f3f4f6;
}

[data-theme="dark"] {
    /* Dark mode colors */
    --bg-primary: #1f2937;
    --bg-secondary: #111827;
    --bg-tertiary: #374151;
    --text-primary: #f9fafb;
    --text-secondary: #9ca3af;
    --text-tertiary: #6b7280;
    --border-color: #374151;
    --hover-bg: #374151;
}
```

### 3. Component Styling

Components use CSS variables instead of hardcoded colors:

```css
.admin-card {
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

.form-input {
    background-color: var(--bg-secondary);
    border-color: var(--border-color);
    color: var(--text-primary);
}
```

## Available CSS Variables

| Variable | Purpose | Light Mode | Dark Mode |
|----------|---------|------------|-----------|
| `--bg-primary` | Primary background | `#ffffff` | `#1f2937` |
| `--bg-secondary` | Secondary background | `#f9fafb` | `#111827` |
| `--bg-tertiary` | Tertiary/accent background | `#f3f4f6` | `#374151` |
| `--text-primary` | Primary text | `#111827` | `#f9fafb` |
| `--text-secondary` | Secondary text | `#6b7280` | `#9ca3af` |
| `--text-tertiary` | Tertiary/muted text | `#9ca3af` | `#6b7280` |
| `--border-color` | Borders | `#e5e7eb` | `#374151` |
| `--hover-bg` | Hover backgrounds | `#f3f4f6` | `#374151` |

## Creating Theme-Aware Components

When creating new components, use CSS variables instead of Tailwind's dark mode classes:

### ❌ Don't Do This:
```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Content
</div>
```

### ✅ Do This:
```html
<div style="background-color: var(--bg-primary); color: var(--text-primary);">
    Content
</div>
```

Or define a CSS class:
```css
.my-component {
    background-color: var(--bg-primary);
    color: var(--text-primary);
}
```

## Switching Themes at Runtime

To switch themes, update your `.env` file and rebuild assets:

```bash
# Update .env
THEME_MODE=light

# Rebuild CSS
npm run build
```

**Note:** No code changes are required when switching themes - the CSS variables automatically adapt based on the `data-theme` attribute.

## Best Practices

1. **Use CSS Variables** - Always use CSS variables for colors that should adapt to theme changes
2. **Test Both Modes** - Test your UI in both light and dark modes to ensure proper contrast
3. **Avoid Hardcoded Colors** - Don't use fixed color values in inline styles
4. **Maintain Contrast** - Ensure text remains readable in both modes

## Examples

### Form Input
```css
.form-input {
    background-color: var(--bg-secondary);
    border-color: var(--border-color);
    color: var(--text-primary);
}
```

### Card Component
```css
.admin-card {
    background-color: var(--bg-primary);
    border-color: var(--border-color);
}

.admin-card-header {
    background-color: var(--bg-primary);
    border-color: var(--border-color);
    color: var(--text-primary);
}
```

### Hover States
```css
.btn-hover {
    background-color: var(--bg-secondary);
}

.btn-hover:hover {
    background-color: var(--hover-bg);
}
```

## Troubleshooting

### Theme Not Changing

1. Check `.env` has correct `THEME_MODE` value
2. Run `npm run build` to rebuild CSS
3. Clear browser cache
4. Verify `data-theme` attribute on body tag

### Colors Look Wrong

1. Check CSS variables are defined for both light and dark modes
2. Ensure components use CSS variables, not hardcoded colors
3. Test in both modes to verify contrast ratios

## Future Enhancements

Potential additions to the theme system:

- Auto mode (follows system preference)
- Custom theme colors per tenant
- Theme switcher UI component
- Additional theme presets (blue, purple, etc.)
- Per-user theme preferences
