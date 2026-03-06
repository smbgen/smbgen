# Web Routes Documentation

This document describes the logic and structure of the application's web routes, with a focus on the root route and CMS integration.

## Root Route (`/`)

The root route is defined as follows:

```php
Route::get('/', function () {
    if (config('business.features.home_landing')) {
        // Try to load CMS page with slug 'home' or 'landing'
        if (config('business.features.cms')) {
            $landingPage = \App\Models\CmsPage::where('slug', 'home')
                ->orWhere('slug', 'landing')
                ->first();
            if ($landingPage) {
                return view('cms.show', ['page' => $landingPage]);
            }
        }
        // Fallback to home view if it exists
        if (view()->exists('home')) {
            return view('home');
        }
        // If no home view exists, show a simple message
        return response()->view('cms.default-landing');
    }
    // Default: redirect to login
    return redirect()->route('login');
});
```

### Logic Analysis
- If the `home_landing` feature is enabled, the route attempts to load a CMS page with the slug `home` or `landing`.
- If a CMS page is found, it is rendered using the `cms.show` view.
- If no CMS page is found, it checks for a Blade view named `home` and renders it if available.
- If neither is found, it falls back to a default landing view (`cms.default-landing`).
- If the `home_landing` feature is not enabled, the route redirects to the login page.

### Special Case: CMS Root Page
If a CMS page with the slug `home` or `landing` exists, it becomes the root page of the site. This is a special case and should be noted in the CMS editor and index views.

## CMS Integration
- The CMS editor and index should highlight if a page is set as the root (slug = `home` or `landing`).
- Creating or editing a CMS page with these slugs will affect the site's root route.

## Other Notable Routes
- `/book` (Booking wizard)
- `/leadform` (Lead form submission)
- `/landing2` (Alternate landing page)
- `/schedule` (Calendly schedule)
- `/cyber-audit-demo` (Demo landing)

Refer to `routes/web.php` for full details.
