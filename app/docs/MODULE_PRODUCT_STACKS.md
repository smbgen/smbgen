# Module Product Stacks

## Purpose

Use modules to ship productized SaaS stacks inside smbgen without bloating the core app routes or forcing business-specific code into the shared platform.

The current reference implementation is the Extreme product stack, implemented in [app/Modules/CleanSlate](../Modules/CleanSlate).

## What A Module Owns

A module should own the business logic that makes a product stack distinct:

- public marketing pages
- onboarding flows
- billing or subscription logic specific to that product
- customer dashboards
- admin tools for that product
- module migrations, views, middleware, and config

The shared smbgen app still owns common platform concerns such as authentication, users, client records, messaging, billing primitives, CMS, and shared admin infrastructure.

## Route Loading

Module routes are auto-discovered from:

- `app/Modules/*/Routes/web.php`

Core route registration happens first. Module routes are loaded after the main web routes and before the CMS catch-all route. That ordering matters:

- core application routes win over generic module slugs
- product-stack routes like `/extreme/*` stay out of `routes/web.php`
- the CMS catch-all remains last so it does not swallow module pages

## Extreme As The Example

Extreme is the example product stack for custom business logic.

It demonstrates how to:

- expose a branded public entry point
- collect product-specific intake data
- run a separate onboarding flow
- enforce product-specific subscription middleware
- add an admin area under its own prefix

Even though the folder is currently named `CleanSlate`, treat it as the example module pattern for future productized stacks.

## Recommended Module Structure

```text
app/Modules/AcmeStack/
  AcmeStackServiceProvider.php
  Config/
    acmestack.php
  Database/
    Migrations/
  Http/
    Controllers/
    Middleware/
  Models/
  Resources/
    Views/
  Routes/
    web.php
```

## Creating A New Product Stack

1. Create a new folder under `app/Modules/<YourStackName>`.
2. Add a service provider for config, views, and migrations.
3. Add `Routes/web.php` inside the module. smbgen will load it automatically.
4. Prefix the public product area clearly, for example `/acme`.
5. Keep shared platform logic in the main app and only place product-specific flows in the module.
6. Register the module service provider in [bootstrap/app.php](../../bootstrap/app.php) so Laravel loads the module config, views, and migrations.

## Route Conventions For Modules

- Public product pages should live under a stable prefix such as `/extreme`.
- Customer routes should usually layer on `auth` and `verified` middleware.
- Product-specific admin routes should live under `/admin/<product>`.
- Route names should be namespaced so they do not collide with the core app.

Example:

```php
Route::prefix('acme')->name('acme.')->group(function () {
    Route::view('/', 'acme::landing')->name('landing');
});

Route::middleware(['auth', 'verified'])
    ->prefix('acme')
    ->name('acme.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
```

## Integration Guidance

Use modules when the product has meaningfully different rules, journeys, or data. Do not use a module just to hide a couple of routes.

Good candidates:

- vertical-specific SaaS offers
- white-labeled workflows
- premium add-on products with separate onboarding
- branded funnels with their own post-purchase experience

Poor candidates:

- generic CRUD screens already supported by smbgen
- minor CMS pages
- isolated helpers that do not form a cohesive product

## Operational Notes

- Keep route files thin and move real logic into controllers or middleware.
- Preserve stable prefixes and route names once a product is live.
- Test module routes independently from the core route files.
- If production uses route caching, refresh the route cache after adding a new module route file.