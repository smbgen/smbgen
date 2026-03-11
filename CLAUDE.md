# Claude Code Instructions — prtl7-app

## PHP Path (Windows + Herd)

The bash shell used by Claude Code does not inherit the Windows PATH, so `php` is
not found even though it is installed. Always use the full path to the Herd PHP binary:

```bash
/c/Users/alexr/.config/herd/bin/php84/php.exe artisan test
/c/Users/alexr/.config/herd/bin/php84/php.exe artisan migrate
/c/Users/alexr/.config/herd/bin/php84/php.exe artisan tinker
```

Alias for convenience — use this in every PHP bash command:
```
PHP=/c/Users/alexr/.config/herd/bin/php84/php.exe
```

**Never** use bare `php` in Bash tool calls. It will return `command not found`.

## Composer

Composer is also a Windows .bat and is NOT available bare in bash. Use:
```bash
/c/Users/alexr/.config/herd/bin/php84/php.exe /c/Users/alexr/.config/herd/bin/composer.phar
```

## Running Tests

```bash
PHP=/c/Users/alexr/.config/herd/bin/php84/php.exe
$PHP artisan test
$PHP artisan test tests/Feature/Auth/
$PHP artisan test --filter=RegistrationTest
```

## Project Stack

- Laravel 12 / PHP 8.4
- Tailwind CSS 3 + Alpine.js + Livewire 3
- Pest PHP for testing
- Herd for local development (Windows)
- SQLite for local DB (default)

## Branch Convention

- `main` — stable
- `feature/*` — feature work, PR into main
- Always run tests before committing: `$PHP artisan test`
- Format code: `vendor/bin/pint` (or `$PHP vendor/bin/pint`)
