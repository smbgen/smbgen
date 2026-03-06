# Livewire directives showing as literal text

Problem

On the login screen, the Blade directive `@livewireStyles` (and potentially `@livewireScripts`) is showing up as literal text instead of being rendered as Livewire's styles/scripts tags.

Cause

This happens when the Livewire package is not installed or its service provider/directives are not registered when Blade compiles the views. Blade doesn't recognize the `@livewireStyles` directive and leaves it as raw text in the compiled view.

What I checked

- `@livewireStyles` appears in:
  - `resources/views/layouts/guest.blade.php`
  - `resources/views/layouts/app.blade.php`
- The login view `resources/views/auth/login.blade.php` extends `layouts.guest`.
- `composer.json` contains `"livewire/livewire": "^3.6"` but the `vendor/livewire` package may be missing if Composer dependencies weren't installed or are out of sync.

Permanent fix (recommended)

Install Livewire and clear compiled caches so Blade recompiles with Livewire's directives available.

Run these in PowerShell from the project root:

```powershell
# install composer dependencies (recommended)
composer install

# OR install Livewire directly (if you prefer just adding it)
composer require livewire/livewire:^3.6

# clear compiled views and caches
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear
composer dump-autoload
```

After that, reload the login page — `@livewireStyles` should render properly.

Quick temporary guard (safe while Livewire is missing)

If you cannot install Livewire immediately, add a guard in your layouts to prevent the literal directive from showing:

```blade
@if (class_exists(\Livewire\Livewire::class))
    @livewireStyles
@endif

...existing code...

@if (class_exists(\Livewire\Livewire::class))
    @livewireScripts
@endif
```

Apply this to:

- `resources/views/layouts/guest.blade.php`
- `resources/views/layouts/app.blade.php`

Why the guard works

Blade compiles the `class_exists(...)` check into PHP. If the Livewire class is absent, the if condition is false and the unrecognized `@livewireStyles` directive will not be executed or output, preventing the raw text from appearing.

Verification steps

1. If you installed Livewire:
   - Confirm `vendor/livewire/livewire` exists.
   - Run `php artisan view:clear` then reload the login page. The directive should no longer appear as text.

2. If you used the guard:
   - Reload the login page; the literal `@livewireStyles` text should not appear. After installing Livewire, remove the guard and clear views so Blade can compile the directive.

Follow-ups

- Optionally, add a short automated test that the login page doesn't contain `@livewireStyles` in the rendered HTML.
- If you want, I can apply the guard edits to the layout files for you. If you prefer I can instead run the composer install and clear caches here.

Document created by automation.
