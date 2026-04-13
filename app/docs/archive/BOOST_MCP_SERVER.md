# Laravel Boost MCP Server

Laravel Boost accelerates AI-assisted development by providing the essential context and structure that AI needs to generate high-quality, Laravel-specific code.

**Last Updated:** October 12, 2025  
**Version:** Laravel Boost v1.3.0+  
**Project:** ClientBridge Laravel

---

## Table of Contents

1. [What is Laravel Boost?](#what-is-laravel-boost)
2. [Installation](#installation)
3. [VS Code Setup](#vs-code-setup)
4. [Available MCP Tools](#available-mcp-tools)
5. [AI Guidelines](#ai-guidelines)
6. [Documentation API](#documentation-api)
7. [Usage Examples](#usage-examples)
8. [Custom Guidelines](#custom-guidelines)
9. [Troubleshooting](#troubleshooting)
10. [Best Practices](#best-practices)

---

## What is Laravel Boost?

At its foundation, Laravel Boost is an MCP server equipped with over 15 specialized tools designed to streamline AI-assisted coding workflows. The package includes:

### Core Components

- **MCP Server** - 16+ Laravel-specific tools for database access, configuration, routing, etc.
- **Composable AI Guidelines** - Framework-appropriate code generation rules for Laravel ecosystem packages
- **Documentation API** - Over 17,000 pieces of Laravel-specific information
- **Semantic Search** - Embeddings-powered search for precise, context-aware results

### Key Features

- **Database Schema Access** - Read table structures, columns, relationships
- **Database Queries** - Execute read-only SQL queries safely
- **Configuration Management** - Access config and environment variables
- **Artisan Commands** - List and understand available commands
- **Route Information** - View all application routes with filtering
- **Error Tracking** - Access last errors and log entries
- **Tinker Integration** - Execute PHP code in Laravel application context
- **Documentation Search** - Search version-specific Laravel ecosystem docs
- **Application Info** - Get Laravel version, PHP version, installed packages
- **Browser Logs** - Read logs and errors from frontend JavaScript

**Important:** Laravel Boost is currently in beta and receives frequent updates as features are refined and expanded.

---

## Installation

### Step 1: Install Laravel Boost via Composer

```bash
composer require laravel/boost --dev
```

### Step 2: Install MCP Server and Guidelines

```bash
php artisan boost:install
```

This command:
- Installs the MCP server configuration
- Downloads AI guidelines for your installed packages
- Sets up the documentation API access
- Creates necessary configuration files

### Step 3: Verify Installation

Check that Boost is properly installed:

```bash
php artisan boost:mcp --help
```

You should see the MCP server help information.

---

## VS Code Setup

### Method 1: Automatic Setup

1. **Open Command Palette** - Press `Ctrl+Shift+P` (or `Cmd+Shift+P` on macOS)
2. **Type "MCP: List Servers"** and press Enter
3. **Select laravel-boost** and press Enter  
4. **Choose "Start server"**

### Method 2: Manual Registration

If automatic setup doesn't work, manually register the MCP server with these details:

**Command:** `php`  
**Args:** `artisan boost:mcp`

**JSON Configuration Example:**
```json
{
    "mcpServers": {
        "laravel-boost": {
            "command": "php",
            "args": ["artisan", "boost:mcp"]
        }
    }
}
```

### Verification

To verify Boost is working in VS Code:

1. Open GitHub Copilot Chat
2. Ask: "Can you use Laravel Boost tools?"
3. You should see confirmation that Boost tools are available

---

## Quick Start

## Available MCP Tools

Laravel Boost provides 16 specialized tools for Laravel development:

### 1. Application Info
**Tool:** `Application Info`

Returns comprehensive application information including:
- PHP version and Laravel version
- All installed packages with their versions
- Available Eloquent models
- Database engine information

**Example Usage:**
```
Get application info to understand this Laravel project
```

### 2. Browser Logs
**Tool:** `Browser Logs`

Reads logs and errors from the browser console for frontend debugging.

**Parameters:**
- `entries` - Number of log entries to return

**Example Usage:**
```
Show me the last 5 browser console errors
```

### 3. Database Connections
**Tool:** `Database Connections`

Inspects available database connections, including the default connection.

**Example Usage:**
```
What database connections are configured in this application?
```

### 4. Database Query
**Tool:** `Database Query`

Executes read-only queries against the database safely.

**Parameters:**
- `query` - SQL query to execute (SELECT, SHOW, EXPLAIN, DESCRIBE only)
- `connection` (optional) - Database connection name

**Example Usage:**
```
Query the users table to show all users created in the last week
```

### 5. Database Schema
**Tool:** `Database Schema`

Reads the complete database schema including tables, columns, data types, indexes, and foreign keys.

**Parameters:**
- `table` (optional) - Specific table name to inspect
- `connection` (optional) - Database connection name

**Example Usage:**
```
Show me the database schema for the bookings and users tables
```

### 6. Get Absolute URL
**Tool:** `Get Absolute URL`

Converts relative path URIs to absolute URLs so agents generate valid links.

**Parameters:**
- `path` (optional) - Relative path like "/dashboard"
- `route` (optional) - Named route like "admin.users.index"

**Example Usage:**
```
What's the absolute URL for the admin dashboard route?
```

### 7. Get Config
**Tool:** `Get Config`

Gets configuration values using Laravel's "dot" notation.

**Parameters:**
- `key` - Configuration key like "app.name" or "database.default"

**Example Usage:**
```
What is the configured app name and mail driver?
```

### 8. Last Error
**Tool:** `Last Error`

Reads the last error from the application's log files.

**Example Usage:**
```
What was the last error that occurred in the application?
```

### 9. List Artisan Commands
**Tool:** `List Artisan Commands`

Inspects all available Artisan commands with descriptions and parameters.

**Parameters:**
- `filter` (optional) - Filter commands by name pattern

**Example Usage:**
```
List all artisan commands related to database migrations
```

### 10. List Available Config Keys
**Tool:** `List Available Config Keys`

Inspects all available configuration keys from config files.

**Example Usage:**
```
Show me all available configuration keys
```

### 11. List Available Env Vars
**Tool:** `List Available Env Vars`

Inspects available environment variable keys (not values for security).

**Example Usage:**
```
What environment variables are defined in this application?
```

### 12. List Routes
**Tool:** `List Routes`

Inspects the application's routes with comprehensive filtering options.

**Parameters:**
- `method` (optional) - Filter by HTTP method (GET, POST, etc.)
- `path` (optional) - Filter by path pattern
- `name` (optional) - Filter by route name
- `domain` (optional) - Filter by domain
- `middleware` (optional) - Filter by middleware

**Example Usage:**
```
Show me all POST routes for the admin area that use auth middleware
```

### 13. Read Log Entries
**Tool:** `Read Log Entries`

Reads the last N entries from Laravel log files.

**Parameters:**
- `count` - Number of log entries to return
- `level` (optional) - Filter by log level (error, warning, info, etc.)

**Example Usage:**
```
Show me the last 10 error-level log entries
```

### 14. Report Feedback
**Tool:** `Report Feedback`

Shares Boost & Laravel AI feedback with the Laravel team.

**Example Usage:**
```
Give Boost feedback: The documentation search is excellent but database queries could be faster
```

### 15. Search Docs
**Tool:** `Search Docs`

Queries the Laravel hosted documentation API service to retrieve documentation based on installed packages.

**Parameters:**
- `query` - Search terms or questions
- `packages` (optional) - Limit search to specific packages
- `version` (optional) - Target specific package versions

**Example Usage:**
```
Search the Laravel docs for database transaction best practices
Search Livewire docs for form validation examples
```

### 16. Tinker
**Tool:** `Tinker`

Executes arbitrary PHP code within the context of the Laravel application.

**Parameters:**
- `code` - PHP code to execute (without <?php tags)
- `timeout` (optional) - Maximum execution time

**Security Note:** Use carefully - can execute any PHP code!

**Example Usage:**
```
Use tinker to count how many users have verified email addresses
```

---

## AI Guidelines

Laravel Boost includes comprehensive AI guidelines for consistent, framework-appropriate code generation:

### Available Guidelines

| Package | Versions Supported |
|---------|-------------------|
| **Core & Boost** | core |
| **Laravel Framework** | core, 10.x, 11.x, 12.x |
| **Livewire** | core, 2.x, 3.x |
| **Filament** | core, 4.x |
| **Flux UI** | core, free, pro |
| **Herd** | core |
| **Inertia Laravel** | core, 1.x, 2.x |
| **Inertia React** | core, 1.x, 2.x |
| **Inertia Vue** | core, 1.x, 2.x |
| **Pest** | core, 4.x |
| **PHPUnit** | core |
| **Pint** | core |
| **Tailwind CSS** | core, 3.x, 4.x |
| **Livewire Volt** | core |
| **Laravel Folio** | core |
| **Enforce Tests** | conditional |

### Keeping Guidelines Updated

Update your local AI guidelines periodically:

```bash
php artisan boost:update
```

Automate updates by adding to `composer.json`:

```json
{
  "scripts": {
    "post-update-cmd": [
      "@php artisan boost:update --ansi"
    ]
  }
}
```

---

## Documentation API

Laravel Boost includes access to a comprehensive documentation API with semantic search:

### Available Documentation

| Package | Versions Supported |
|---------|-------------------|
| **Laravel Framework** | 10.x, 11.x, 12.x |
| **Filament** | 2.x, 3.x, 4.x |
| **Flux UI** | 2.x Free, 2.x Pro |
| **Inertia** | 1.x, 2.x |
| **Livewire** | 1.x, 2.x, 3.x |
| **Nova** | 4.x, 5.x |
| **Pest** | 3.x, 4.x |
| **Tailwind CSS** | 3.x, 4.x |

### Documentation Features

- **Version-Specific Results** - Documentation matches your exact package versions
- **Semantic Search** - AI-powered search using embeddings for better context matching
- **17,000+ Information Pieces** - Comprehensive coverage of Laravel ecosystem
- **Context-Aware Results** - Search results prioritized by relevance to your project

---

## Usage Examples

### Getting Started with a Project

```
# Start with application context
Get application info

# Understand the database structure  
Show me the database schema

# See what routes are available
List all routes grouped by middleware
```

### Debugging an Issue

```
# Check recent errors
What was the last error?
Show me the last 20 log entries

# Examine related database state
Show me the schema for the users table
Query the users table for any accounts created today
```

### Building a New Feature

```
# Research the approach
Search Laravel docs for rate limiting middleware
Search Livewire docs for real-time form validation

# Check existing patterns
Show me all routes that use the RateLimiter middleware
List artisan commands for generating middleware
```

### Understanding Configuration

```
# Check current settings
What's the configured app environment and database connection?
List all available config keys for mail settings

# Verify environment
What environment variables are defined for queue configuration?
```

### Database Investigation

```
# Explore structure
Show me all database connections
Show me the schema for bookings, users, and payments tables

# Query data safely
Count all bookings by status in the last month
Show me the 10 most recent user registrations
```

---

## Custom Guidelines

You can extend Laravel Boost with your own AI guidelines:

### Adding Custom Guidelines

Create `.blade.php` files in your application's `.ai/guidelines/*` directory:

**Example: `.ai/guidelines/custom/api-standards.blade.php`**

```blade
## API Standards

This application follows specific API conventions:

### Response Format
- All API responses use JSON:API format
- Include `data`, `meta`, and `links` sections
- Use HTTP status codes consistently

### Authentication  
- Use Laravel Sanctum for API authentication
- Include rate limiting on all API routes
- Validate API tokens in middleware

### Example API Controller:

@verbatim
<code-snippet name="Standard API Controller" lang="php">
class ApiController extends Controller
{
    public function index(Request $request)
    {
        $data = Model::query()
            ->when($request->filter, fn($q) => $q->where('name', 'like', "%{$request->filter}%"))
            ->paginate();

        return response()->json([
            'data' => $data->items(),
            'meta' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
            ],
            'links' => [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
            ]
        ]);
    }
}
</code-snippet>
@endverbatim
```

### Overriding Boost Guidelines

Override built-in guidelines by creating files with matching paths:

**Example:** Override Inertia React v2 forms guidance:
Create: `.ai/guidelines/inertia-react/2/forms.blade.php`

### Third-Party Package Guidelines

Package authors can include guidelines by adding:
`resources/boost/guidelines/core.blade.php` to their package.

---

## Troubleshooting

### Issue: Boost Tools Not Available in VS Code

**Solutions:**

1. **Verify MCP Server Registration:**
   ```bash
   # Check if server starts correctly
   php artisan boost:mcp --help
   ```

2. **Restart VS Code:**
   - Completely quit and restart VS Code
   - Reload the window: `Ctrl+Shift+P` → "Developer: Reload Window"

3. **Check MCP Settings:**
   - Open Command Palette: `Ctrl+Shift+P`
   - Type "MCP: List Servers"
   - Ensure `laravel-boost` is listed and enabled

4. **Manual Configuration:**
   Add to MCP settings manually if auto-detection fails

### Issue: Database Connection Errors

**Solutions:**

1. **Verify Laravel Database Connection:**
   ```bash
   php artisan migrate:status
   ```

2. **Check Database Configuration:**
   ```bash
   php artisan config:show database
   ```

3. **Test Database Query in Tinker:**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

### Issue: Documentation Search Returns No Results

**Solutions:**

1. **Update Guidelines:**
   ```bash
   php artisan boost:update
   ```

2. **Check Package Installation:**
   ```bash
   composer show | grep laravel
   ```

3. **Try Broader Search Terms:**
   - Use multiple related keywords
   - Search for general concepts rather than specific method names

### Issue: Tinker Execution Timeouts

**Solutions:**

1. **Optimize Queries:**
   - Add database indexes
   - Use `limit()` and `take()` for large datasets
   - Break complex operations into smaller chunks

2. **Increase Memory Limit:**
   ```bash
   php -d memory_limit=512M artisan tinker
   ```

### Issue: Permission Denied Reading Logs

**Solutions:**

1. **Fix Log Permissions:**
   ```bash
   chmod -R 755 storage/logs
   ```

2. **Check Storage Permissions:**
   ```bash
   chmod -R 755 storage/
   chown -R www-data:www-data storage/  # Linux/macOS
   ```

### Issue: Artisan Commands Not Listed

**Solutions:**

1. **Clear Application Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Autoload Classes:**
   ```bash
   composer dump-autoload
   ```

---

## Best Practices

### 1. Start Sessions with Context

Always begin AI conversations with:
```
Get application info and show me the database schema
```

This gives the AI full context about your Laravel application.

### 2. Use Documentation Search First

Before implementing features:
```
Search Laravel docs for the best way to implement file uploads
Search Livewire docs for real-time validation patterns
```

### 3. Leverage Database Schema for Relationships

```
# Good - Get full context
Show me the schema for users, bookings, and payments tables

# Less helpful - Piecemeal approach  
What columns does the users table have?
```

### 4. Filter Routes Effectively

```
# Specific and useful
Show me all POST routes for /admin that use the auth middleware

# Too broad
List all routes
```

### 5. Use Tinker for Quick Verification

```
# Instead of writing test files
Use tinker to check if the User model has a bookings relationship
```

### 6. Check Logs Before Debugging

```
What was the last error and show me the last 10 log entries
```

Often reveals issues immediately.

### 7. Be Specific with Documentation Searches

```
# Good - Specific and actionable
Search docs for database transaction rollback patterns

# Less helpful - Too broad
Search docs for database
```

### 8. Keep Guidelines Updated

Run `php artisan boost:update` regularly to ensure AI guidelines match your package versions.

### 9. Use Absolute URLs for Links

```
Get the absolute URL for the user dashboard route
```

Ensures generated links work correctly in all environments.

### 10. Combine Multiple Tools

```
# Comprehensive debugging approach
Get application info
Show me the last error  
Show me the last 10 log entries
Query the database to check for any failed jobs
```

---

## Advanced Configuration

### Environment-Specific Settings

Different settings for different environments in your application:

**Development:**
```php
// config/boost.php (if you create one)
return [
    'enabled' => env('BOOST_ENABLED', true),
    'tools' => [
        'tinker' => env('APP_ENV') !== 'production',
        'database' => true,
        'logs' => true,
    ],
    'security' => [
        'read_only' => env('APP_ENV') === 'production',
        'max_query_time' => 30,
    ],
];
```

### Performance Optimization

For large applications:

1. **Limit Database Query Results:**
   ```
   Query the users table but limit to 100 results
   ```

2. **Use Specific Table Filters:**
   ```
   Show me only the bookings and payments table schemas
   ```

3. **Filter Log Entries:**
   ```
   Show me the last 5 error-level log entries only
   ```

---

## Security Considerations

### Read-Only Database Access

Laravel Boost uses read-only database access by default for security. Only SELECT, SHOW, EXPLAIN, and DESCRIBE queries are allowed.

### Environment Variable Protection

Environment variable values are never exposed - only variable names are shown for security.

### Code Execution Limits

Tinker execution has built-in timeouts and memory limits to prevent resource abuse.

### Log File Access

Only application logs are accessible, not system logs or sensitive files.

---

## Integration with Other Tools

### Laravel Herd

Laravel Boost works seamlessly with Laravel Herd:
- Automatic project detection
- No additional server configuration needed
- Works with Herd's PHP version switching

### Laravel Sail

For Docker-based development:
```bash
./vendor/bin/sail artisan boost:install
./vendor/bin/sail artisan boost:mcp
```

### Pest Testing Framework

Use Boost to understand your test structure:
```
List all artisan commands related to testing
Search Pest docs for feature test examples
```

---

## Resources

### Official Links
- **Laravel Boost Repository:** https://github.com/laravel/boost
- **Laravel Documentation:** https://laravel.com/docs
- **Model Context Protocol:** https://modelcontextprotocol.io

### Community Resources
- **Laravel Discord:** https://discord.gg/laravel
- **Laracasts:** https://laracasts.com
- **Laravel News:** https://laravel-news.com

### Development Tools
- **Laravel Herd:** https://herd.laravel.com
- **VS Code:** https://code.visualstudio.com
- **GitHub Copilot:** https://github.com/features/copilot

---

## Conclusion

Laravel Boost significantly enhances AI-assisted Laravel development by providing:

✅ **Deep Application Context** - AI understands your exact Laravel setup and packages  
✅ **Safe Database Access** - Query your database without risk of modifications  
✅ **Version-Specific Documentation** - Search docs that match your package versions  
✅ **Comprehensive Debugging** - Access logs, errors, and application state instantly  
✅ **Framework Best Practices** - AI guidelines ensure Laravel-appropriate code generation  
✅ **Seamless Integration** - Works naturally with VS Code and GitHub Copilot

### Quick Start Checklist

1. ✅ Install: `composer require laravel/boost --dev`
2. ✅ Setup: `php artisan boost:install`  
3. ✅ Configure VS Code MCP server
4. ✅ Start conversations with: "Get application info"
5. ✅ Use documentation search before implementing features
6. ✅ Keep guidelines updated: `php artisan boost:update`

**Remember:** Laravel Boost is most effective when you start each AI conversation with "Get application info" to provide full context about your Laravel project!

---

**Document Version:** 2.0.0  
**Last Updated:** October 12, 2025  
**Laravel Boost Version:** v1.3.0+  
**Project:** ClientBridge Laravel  
**Author:** Comprehensive guide based on official Laravel Boost documentation