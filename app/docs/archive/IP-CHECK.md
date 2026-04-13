Here’s an updated and structured documentation snippet with all relevant files and paths added. This is perfect for your `README.md` or internal docs:

---

## 🔐 IP-Based Access Restriction (Experimental Feature)

This project includes optional middleware to **restrict access based on the visitor's IP address**. While currently **disabled**, the implementation is ready for production with a quick toggle.

---

### 📁 Affected Files & Paths

* `app/Http/Middleware/CheckApprovedIP.php`
  → Middleware logic to check against a whitelist of approved IPs.

* `bootstrap/app.php`
  → Registers the `check.ip` middleware alias:

  ```php
  $middleware->alias([
      'check.ip' => \App\Http\Middleware\CheckApprovedIP::class,
  ]);
  ```

* `config/approved_ips.php`
  → Configuration file containing an array of approved IP addresses:

  ```php
  return [
      'ips' => [
          '127.0.0.1',
          '192.168.1.50',
          '1.2.3.4',
      ],
  ];
  ```

* `resources/views/errors/denied-ip.blade.php`
  → Custom error page shown when a user from an unapproved IP accesses a protected route.

* `routes/web.php`
  → Example route using the middleware:

  ```php
  Route::get('/test-ip', fn () => 'Access granted')->middleware(['check.ip']);
  ```

---

### 🧪 Usage Notes

**To enable protection:**

* Add `->middleware('check.ip')` to any route or route group.

**To exclude from protection (e.g., logout):**

```php
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->withoutMiddleware(['check.ip'])
    ->name('logout');
```

---

### 🌍 Configuring IP Access

Approved IPs are defined in `config/approved_ips.php`.

You may also dynamically generate this list using environment variables or a database query if needed.

---

### 🧱 Sample Middleware Logic

**`CheckApprovedIP.php`:**

```php
public function handle($request, Closure $next)
{
    $allowedIps = config('approved_ips.ips');
    if (!in_array($request->ip(), $allowedIps)) {
        return response()->view('errors.denied-ip', [], 403);
    }
    return $next($request);
}
```

---

Let me know if you want this exported to a file or linked in your Laravel UI.
