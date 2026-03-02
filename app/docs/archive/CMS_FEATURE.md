# CMS Feature Documentation

## Overview
The CMS (Content Management System) feature allows administrators to create and manage custom content pages for the ClientBridge application. This feature is controlled by a feature flag and provides a simple yet powerful interface for content management.

## Feature Flag
- **Environment Variable**: `FEATURE_CMS`
- **Default**: `false` (disabled)
- **Config Key**: `business.features.cms`

### Enabling the Feature
Add to your `.env` file:
```bash
FEATURE_CMS=true
```

## Database Schema

### `cms_pages` Table
| Column | Type | Description |
|--------|------|-------------|
| id | integer | Primary key |
| slug | string (unique) | URL-friendly identifier (e.g., "home", "about") |
| title | string | Page title |
| head_content | text (nullable) | Custom HTML for `<head>` section (meta tags, CSS, scripts) |
| body_content | text (nullable) | Main page HTML content |
| cta_text | string (nullable) | Call-to-action button text |
| cta_url | string (nullable) | Call-to-action button URL |
| is_published | boolean | Publication status (default: false) |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

### Indexes
- `slug` - For fast lookups by slug
- `is_published` - For filtering published pages
- `slug` (unique) - Ensures unique slugs

## Routes

All CMS routes are protected by authentication and company administrator middleware, and are only registered when the feature flag is enabled.

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | /admin/cms | admin.cms.index | List all CMS pages |
| GET | /admin/cms/create | admin.cms.create | Show create page form |
| POST | /admin/cms | admin.cms.store | Store a new page |
| GET | /admin/cms/{cmsPage}/edit | admin.cms.edit | Show edit page form |
| PUT | /admin/cms/{cmsPage} | admin.cms.update | Update an existing page |
| DELETE | /admin/cms/{cmsPage} | admin.cms.destroy | Delete a page |

## Model

### CmsPage Model

#### Fillable Attributes
- slug
- title
- head_content
- body_content
- cta_text
- cta_url
- is_published

#### Casts
- `is_published` → boolean

#### Scopes

**`published()`**
Returns only published pages:
```php
$publishedPages = CmsPage::published()->get();
```

#### Static Methods

**`findBySlug(string $slug)`**
Find a page by its slug:
```php
$page = CmsPage::findBySlug('home');
```

## Admin Interface

### Dashboard Integration
When the feature flag is enabled, the "CMS Editor" card in the admin dashboard links to the CMS management interface:
```php
@if(config('business.features.cms'))
    <a href="{{ route('admin.cms.index') }}">Manage Pages</a>
@else
    Enable via FEATURE_CMS=true in .env
@endif
```

### Page Management Interface

#### Index Page (`/admin/cms`)
- Lists all CMS pages in a table
- Shows: slug, title, status, CTA, last updated
- Actions: Edit, Delete
- "Create New Page" button

#### Create/Edit Forms
Fields:
1. **Slug** (required) - URL-friendly identifier
2. **Title** (required) - Page title
3. **Head Content** (optional) - Custom HTML for `<head>` section
4. **Body Content** (optional) - Main page HTML
5. **CTA Text** (optional) - Button text
6. **CTA URL** (optional) - Button URL
7. **Published** (checkbox) - Publication status

## Validation Rules

### Create Page
```php
[
    'slug' => 'required|string|max:50|unique:cms_pages,slug|alpha_dash',
    'title' => 'required|string|max:255',
    'head_content' => 'nullable|string',
    'body_content' => 'nullable|string',
    'cta_text' => 'nullable|string|max:100',
    'cta_url' => 'nullable|string|max:255',
    'is_published' => 'boolean',
]
```

### Update Page
Same as create, except `slug` uniqueness check excludes current page:
```php
'slug' => 'required|string|max:50|alpha_dash|unique:cms_pages,slug,' . $cmsPage->id
```

## Usage Examples

### Creating a Home Page
```php
$page = CmsPage::create([
    'slug' => 'home',
    'title' => 'Welcome to Our Site',
    'head_content' => '<meta name="description" content="Welcome to our amazing site">',
    'body_content' => '<div class="hero">Welcome!</div>',
    'cta_text' => 'Get Started',
    'cta_url' => '/book',
    'is_published' => true,
]);
```

### Fetching Published Pages
```php
// Get all published pages
$pages = CmsPage::published()->get();

// Get a specific page by slug
$homePage = CmsPage::findBySlug('home');
```

### Rendering CMS Content
```blade
@php
    $page = \App\Models\CmsPage::findBySlug('home');
@endphp

@if($page && $page->is_published)
    <head>
        {!! $page->head_content !!}
    </head>
    <body>
        {!! $page->body_content !!}
        
        @if($page->cta_text && $page->cta_url)
            <a href="{{ $page->cta_url }}" class="btn-primary">
                {{ $page->cta_text }}
            </a>
        @endif
    </body>
@endif
```

## Testing

### Running Tests
```bash
php artisan test --filter=CmsPageTest
```

### Test Coverage
- ✓ Admin can view CMS pages index
- ✓ Admin can create a CMS page
- ✓ Admin can update a CMS page
- ✓ Admin can delete a CMS page
- ✓ Published scope only returns published pages
- ✓ findBySlug returns the correct page
- ~ Feature flag visibility check (routes registered at boot)

## Security Considerations

1. **Authentication Required**: All routes require authentication and company administrator role
2. **HTML Injection**: `head_content` and `body_content` allow raw HTML - only administrators have access
3. **Slug Validation**: Slugs must be alpha-dash (alphanumeric with hyphens and underscores)
4. **XSS Protection**: Use `{!! !!}` syntax carefully when rendering CMS content
5. **Feature Flag**: Feature can be disabled completely via configuration

## Future Enhancements

Potential improvements for the CMS feature:
- [ ] Rich text editor (TinyMCE/CKEditor) for body content
- [ ] Image upload functionality
- [ ] Page templates/layouts
- [ ] Revision history
- [ ] Page preview before publishing
- [ ] SEO fields (meta description, keywords, og tags)
- [ ] Multi-language support
- [ ] Page ordering/hierarchy
- [ ] Custom CSS/JS per page
- [ ] Draft scheduling/publishing dates

## Troubleshooting

### Routes Not Found
**Problem**: Getting 404 errors when accessing CMS routes  
**Solution**: Ensure `FEATURE_CMS=true` in your `.env` file and restart the server

### Can't Create Pages
**Problem**: "Table 'cms_pages' doesn't exist"  
**Solution**: Run migrations: `php artisan migrate`

### Duplicate Slug Error
**Problem**: "The slug has already been taken"  
**Solution**: Slugs must be unique. Use a different slug or delete the existing page first

## Migration
```bash
# Run the CMS migration
php artisan migrate

# Rollback if needed
php artisan migrate:rollback --step=1
```

## Files Created/Modified

### New Files
- `database/migrations/2025_10_12_203706_create_cms_pages_table.php`
- `app/Models/CmsPage.php`
- `app/Http/Controllers/Admin/CmsPageController.php`
- `resources/views/admin/cms/index.blade.php`
- `resources/views/admin/cms/create.blade.php`
- `resources/views/admin/cms/edit.blade.php`
- `tests/Feature/Admin/CmsPageTest.php`
- `app/docs/CMS_FEATURE.md` (this file)

### Modified Files
- `config/business.php` - Added `cms` feature flag
- `routes/web.php` - Added CMS routes
- `resources/views/admin/dashboard.blade.php` - Updated CMS Editor card
- `tests/TestCase.php` - Added DatabaseMigrations trait

## Support
For questions or issues with the CMS feature, refer to the Laravel Boost guidelines or consult the development team.
