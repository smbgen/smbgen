# Common Artisan Commands — Quick Reference

This file collects the most useful `php artisan` commands we use during development, debugging, and deployment for the SMBGen app. Each command is provided with a short explanation, recommended usage, and notes for Windows + Bash (WSL / Git Bash / mingw) where applicable.

Notes:
- Run commands from the repository root (where `artisan` is located).
- Use the project's PHP binary (e.g., `php`, or a full path to a PHP executable) if you have multiple PHP versions.
- Commands are safe to copy-paste; for long-running processes (workers, dev server) consider running them in a separate terminal or with a multiplexer.

## Cache & config

- Clear compiled views

```bash
php artisan view:clear
```

- Clear config cache (useful after changing `config/*.php` or `.env` values)

```bash
php artisan config:clear
```

- Rebuild configuration cache (production)

```bash
php artisan config:cache
```

- Clear route cache

```bash
php artisan route:clear
```

- Rebuild route cache (production)

```bash
php artisan route:cache
```

- Clear application cache

```bash
php artisan cache:clear
```

Common pattern when changing views/config/routes during development:

```bash
php artisan view:clear && php artisan config:clear && php artisan route:clear && php artisan cache:clear
```

If you see stale Blade output after fixing templates, clear compiled views (`view:clear`) and, if you maintain caches in production, clear/rebuild the relevant caches.

## Environment & debugging

- Show current configuration values (example)

```bash
php artisan tinker --execute "dump(config('app.env'), config('app.debug'))"
```

- Tail Laravel logs (PowerShell / Bash)

```bash
# Bash / WSL
tail -n 200 storage/logs/laravel.log

# PowerShell
Get-Content storage/logs/laravel.log -Tail 200 -Wait
```

- Run queued jobs immediately (sync driver for local debugging)

```bash
# Temporarily switch QUEUE_CONNECTION=sync in .env
php artisan queue:work --once
```

## Database

- Run migrations

```bash
php artisan migrate
```

- Refresh migrations (drops all tables then re-runs)

```bash
php artisan migrate:refresh --seed
```

- Run seeders

```bash
php artisan db:seed --class=DatabaseSeeder
```

- Rollback last migration batch

```bash
php artisan migrate:rollback
```

## Storage & files

- Link storage (for public access to storage/app/public)

```bash
php artisan storage:link
```

- Clear compiled views that may reference non-existent routes after edits

```bash
php artisan view:clear
```

## Development servers & assets

- Start a local PHP dev server (quick tests)

```bash
php -S localhost:8000 -t public
```

- Vite dev server (frontend assets)

```bash
npm run dev
# or
pnpm dev
```

- Run tests (Pest / PHPUnit)

```bash
# Pest (recommended if available)
./vendor/bin/pest

# Or PHPUnit directly
./vendor/bin/phpunit
```

## Queue workers & scheduling

- Run the queue worker (foreground)

```bash
php artisan queue:work
```

- Run the scheduler once

```bash
php artisan schedule:run
```

## Routes & debugging

- List routes filtered by path or name (useful to confirm route names)

```bash
# On systems where --columns is supported (Laravel versions with column filters):
php artisan route:list --path=documents --columns=method,uri,name,action

# If the --columns option errors on your system, use the default output and filter with grep:
php artisan route:list | grep documents
```

Notes: some older Laravel versions or custom console command configurations might not support the `--columns` option — if you get an error like "The \"--columns\" option does not exist.", omit `--columns` and filter the output with `grep`/`findstr` instead.

## Common troubleshooting tips

- "Route not defined" ViewExceptions
  - If you patch a Blade template to remove or guard a route call, you may still see the old error because Laravel uses compiled Blade files. Run:

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

  - Also check `storage/framework/views` for any stale compiled files and remove them if necessary.

- Artisan `--columns` option error
  - This project may be running an older Laravel or have custom console settings where `--columns` isn't available. Use `php artisan route:list | grep <term>` as a fallback.

- File uploads and storage permissions
  - Ensure `storage/` and `bootstrap/cache` are writable by the web server user. On Windows, this is usually not an issue for local dev but matters on Linux servers.

- Missing environment variables causing view crashes
  - Use defensive getters in Blade: `data_get(config('business'), 'features.some_flag', false)` or cast with `(bool)`.

## Appendix: Useful one-liners

- Clear caches and restart queue worker (useful during a deploy):

```bash
php artisan view:clear && php artisan config:clear && php artisan route:clear && php artisan cache:clear && php artisan queue:restart
```

- Show last 200 lines of log and follow new lines:

```bash
# Bash
tail -n 200 -f storage/logs/laravel.log
```

- Count routes matching 'documents':

```bash
php artisan route:list | grep documents | wc -l
```

---

If you'd like, I can also add project-specific commands (e.g., custom seeder names, package publish commands, or the deploy script entry points from `deployment-templates/`), or add a short troubleshooting checklist that maps common symptoms to commands. Would you like me to include those? 
