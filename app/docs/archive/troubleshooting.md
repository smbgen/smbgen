# Troubleshooting: Clearing caches & regenerating autoload

This file documents the common Laravel troubleshooting commands useful when Blade directives (like `@livewireStyles`) are rendering unexpectedly or when package installation changes are not picked up.

Commands run

Run these from the project root (PowerShell):

```powershell
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize:clear
composer dump-autoload
```

What each command does

- `php artisan view:clear` — removes compiled Blade templates so views are recompiled on next request.
- `php artisan cache:clear` — clears the application cache.
- `php artisan config:clear` — clears the configuration cache so changes to `config/*.php` are used immediately.
- `php artisan route:clear` — clears the route cache.
- `php artisan optimize:clear` — clears several caches (bootstrap, compiled files, etc.).
- `composer dump-autoload` — regenerates Composer's autoloader files and triggers `artisan package:discover`.

Why this helps

- Blade directives provided by packages (like Livewire) are registered by the package discovery/loading process. If the package wasn't installed or the autoloader wasn't regenerated, Blade might not recognize custom directives and will render them as literal text.
- Clearing views and caches forces Laravel to recompile and re-register directives and other bindings.

Verification

- After running the commands, reload the affected page (e.g., the login page). The raw directive text should no longer appear.
- Confirm the package is present in vendor, for example `Test-Path vendor\livewire\livewire` (PowerShell) or `ls vendor/livewire`.

Follow-ups

- If the problem persists, ensure `livewire/livewire` is installed in `composer.json` and run `composer require livewire/livewire:^3.6`.
- If you used the Blade guard in `resources/views/layouts/*.blade.php`, remove it once Livewire is installed and views are recompiled.

Record of command output

- The commands completed successfully during this session and `composer dump-autoload` showed that `livewire/livewire` was discovered.

Document created by automation.
