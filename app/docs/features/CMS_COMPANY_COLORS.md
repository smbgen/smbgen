# CMS Company Colors System

## Overview

The Company Colors system provides a global color scheme that's automatically injected into all CMS pages, making it easy to maintain consistent branding across your site.

## Features

- **Global Configuration**: Set colors once in the admin panel, use everywhere
- **Auto-injection**: CSS variables and utility classes automatically available on all CMS pages
- **Live Preview**: See generated CSS before saving
- **Color Picker**: Visual color selection with hex input fallback
- **Pre-built Classes**: Ready-to-use button and utility classes

## Configuration

Navigate to: **Admin > CMS > Company Colors** (collapsible section)

### Available Colors

| Color | Variable | Purpose | Default |
|-------|----------|---------|---------|
| Primary | `--company-primary` | Main brand color, buttons, links | #3B82F6 |
| Secondary | `--company-secondary` | Accents and highlights | #10B981 |
| Background | `--company-bg` | Page background | #1f2937 |
| Text | `--company-text` | Default text color | #ffffff |
| Accent | `--company-accent` | Badges, alerts | #F59E0B |

## Usage in CMS Pages

### CSS Variables

Use these variables in your inline styles or custom CSS:

```html
<div style="background-color: var(--company-primary); color: var(--company-text);">
    Welcome to our site!
</div>
```

### Pre-built Utility Classes

The system automatically provides these ready-to-use classes:

#### Button Classes

```html
<!-- Primary button -->
<button class="btn-primary">Book Now</button>

<!-- Secondary button -->
<button class="btn-secondary">Learn More</button>
```

#### Color Utility Classes

```html
<!-- Text color -->
<h1 class="text-brand">Heading in brand color</h1>

<!-- Background color -->
<div class="bg-brand">Section with brand background</div>
```

## Technical Details

### Database Table

`cms_company_colors` - Singleton table (only one record)

**Schema:**
- `primary_color` - VARCHAR(7) - Hex color code
- `secondary_color` - VARCHAR(7) - Hex color code
- `background_color` - VARCHAR(7) - Hex color code
- `text_color` - VARCHAR(7) - Hex color code
- `accent_color` - VARCHAR(7) - Hex color code
- `auto_inject_css` - BOOLEAN - Enable/disable auto-injection

### Model

`App\Models\CmsCompanyColors`

**Key Methods:**
- `getSettings()` - Retrieve singleton settings
- `generateCSS()` - Generate CSS string with variables and classes

### Auto-Injection

When `auto_inject_css` is enabled (default), the CSS is automatically injected into the `<head>` of all CMS pages via `layouts/cms.blade.php`.

**Order of injection:**
1. Base Tailwind CSS
2. Company Colors CSS (auto-injected)
3. Page-specific head content

### Generated CSS Structure

```css
:root {
  --company-primary: #3B82F6;
  --company-secondary: #10B981;
  --company-bg: #1f2937;
  --company-text: #ffffff;
  --company-accent: #F59E0B;
}

.btn-primary {
  background-color: var(--company-primary);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: opacity 0.2s;
}

.btn-primary:hover {
  opacity: 0.9;
}

.btn-secondary {
  background-color: var(--company-secondary);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: opacity 0.2s;
}

.btn-secondary:hover {
  opacity: 0.9;
}

.text-brand {
  color: var(--company-primary);
}

.bg-brand {
  background-color: var(--company-primary);
}
```

## Best Practices

1. **Use Variables Over Hardcoded Colors**: Always use `var(--company-primary)` instead of hardcoding hex values
2. **Test Color Contrast**: Ensure text remains readable on backgrounds
3. **Consistent Application**: Use the pre-built classes consistently across pages
4. **Auto-Injection**: Keep auto-injection enabled unless you have specific per-page color requirements

## Examples

### Hero Section

```html
<div style="background-color: var(--company-bg); padding: 4rem 2rem; text-align: center;">
    <h1 style="color: var(--company-primary); font-size: 3rem; font-weight: bold; margin-bottom: 1rem;">
        Welcome to Our Company
    </h1>
    <p style="color: var(--company-text); font-size: 1.25rem; margin-bottom: 2rem;">
        Your trusted partner for innovative solutions
    </p>
    <button class="btn-primary" style="margin-right: 1rem;">Get Started</button>
    <button class="btn-secondary">Learn More</button>
</div>
```

### Feature Card

```html
<div style="background: white; border-left: 4px solid var(--company-primary); padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <h3 class="text-brand" style="font-size: 1.5rem; font-weight: 600; margin-bottom: 0.5rem;">
        Amazing Feature
    </h3>
    <p style="color: #666; margin-bottom: 1rem;">
        Description of this amazing feature and how it helps your customers.
    </p>
    <button class="btn-primary">Try It Now</button>
</div>
```

### Alert Banner

```html
<div style="background-color: var(--company-accent); color: white; padding: 1rem; text-align: center; border-radius: 0.5rem;">
    <strong>Special Offer:</strong> Save 20% on all services this month!
</div>
```

## Relationship with Theme System

The Company Colors system is separate from the admin panel theme system ([THEME_SYSTEM.md](../architecture/THEME_SYSTEM.md)):

- **Theme System**: Controls admin panel appearance (light/dark mode)
- **Company Colors**: Controls public-facing CMS page branding

Both systems use CSS variables but serve different purposes and don't conflict.

## Troubleshooting

### Colors Not Appearing

1. Check that `auto_inject_css` is enabled in Company Colors settings
2. Verify you're viewing a CMS page (system only injects into CMS layout)
3. Clear browser cache and hard reload (Ctrl+Shift+R)

### Inline Styles vs Classes

If you need maximum control, use inline styles with CSS variables. If you want convenience and consistency, use the pre-built classes.

### Custom Classes Not Working

Remember: Only `.btn-primary`, `.btn-secondary`, `.text-brand`, and `.bg-brand` are provided by default. For custom classes, add them to the page's Head Content field.

## Future Enhancements

Potential additions:
- More pre-built utility classes
- Gradient support
- Per-page color overrides
- Color palette presets
- Dark/light mode variants
