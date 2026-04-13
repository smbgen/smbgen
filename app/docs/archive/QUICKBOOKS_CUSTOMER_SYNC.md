# QuickBooks Customer Sync

## Overview

This feature allows you to import and sync customer data between QuickBooks Online and the ClientBridge clients table. Customers can be imported in bulk or synced individually to maintain consistency between systems.

## Features

- ✅ **Bulk Import**: Import all QuickBooks customers at once
- ✅ **Auto-Sync**: Automatically link existing clients by email
- ✅ **Bi-directional Support**: Keep QuickBooks ID in clients table
- ✅ **Smart Matching**: Find or create customers by email
- ✅ **CLI & Web UI**: Import via Artisan command or dashboard
- ✅ **Progress Tracking**: Real-time import progress and statistics

## Database Schema

The `clients` table includes these QuickBooks sync fields:

```php
$table->string('quickbooks_customer_id')->nullable();
$table->timestamp('quickbooks_synced_at')->nullable();
```

## Usage

### Web UI - Dashboard Import

1. Go to **Admin Dashboard**
2. Find the **QuickBooks Integration** panel
3. Ensure QuickBooks is connected
4. Click **Import Customers** button
5. Confirm the import action
6. View import results in success message

### CLI - Artisan Command

**Basic Import:**
```bash
php artisan quickbooks:import-customers
```

**Import with Sync Mode** (updates existing clients):
```bash
php artisan quickbooks:import-customers --sync
```

**Limit Number of Customers:**
```bash
php artisan quickbooks:import-customers --limit=50
```

**Combined Options:**
```bash
php artisan quickbooks:import-customers --sync --limit=200
```

## Import Behavior

### New Customers
- Creates new client record in `clients` table
- Sets `quickbooks_customer_id` to QuickBooks ID
- Sets `quickbooks_synced_at` to current timestamp
- Sets `notes` to "Imported from QuickBooks"

### Existing Customers (Normal Mode)
- **By default**: Skips if email already exists
- No changes made to existing records
- Use `--sync` flag to update QB IDs

### Existing Customers (Sync Mode)
- Updates `quickbooks_customer_id` if not already set
- Updates `quickbooks_synced_at` to current timestamp
- Preserves all other client data

### Skipped Customers
- QuickBooks customers without email addresses
- Existing clients already synced (in normal mode)
- Invalid or malformed customer data

## Service Layer

### QuickBooksService Methods

```php
// Get all customers from QuickBooks
$service->getAllCustomers(?User $user = null, int $limit = 1000): ?array

// Sync a Client model with QuickBooks
$service->syncClientWithQuickBooks(Client $client, ?User $user = null): array
```

### Example: Manual Sync

```php
use App\Models\Client;
use App\Services\QuickBooksService;

$client = Client::find(1);
$qbService = app(QuickBooksService::class);

$result = $qbService->syncClientWithQuickBooks($client);

if ($result['success']) {
    echo "Synced! QuickBooks ID: " . $result['customer_id'];
}
```

## Import Statistics

After import, you'll see:

- **Imported**: Number of new clients created
- **Updated**: Number of existing clients updated (sync mode only)
- **Skipped**: Number of customers not imported
  - No email address
  - Already exists (normal mode)
  - Already synced (sync mode)
- **Errors**: Number of failed imports (see logs)

## CLI Output Example

```
🔄 Starting QuickBooks customer import...

📥 Fetching customers from QuickBooks (limit: 100)...
✓ Found 45 customers in QuickBooks

 45/45 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

═══════════════════════════════════════════════════════════
  IMPORT COMPLETE
═══════════════════════════════════════════════════════════

✅ Imported:  23 new clients
⏭️  Skipped:   22 (no email or already exists)

💡 Tip: Use --sync flag to update existing clients with QuickBooks IDs
```

## Use Cases

### Initial Setup
Import all QuickBooks customers when setting up ClientBridge:
```bash
php artisan quickbooks:import-customers
```

### Regular Sync
Keep client QuickBooks IDs up to date:
```bash
php artisan quickbooks:import-customers --sync
```

### Large Dataset
Import customers in batches:
```bash
php artisan quickbooks:import-customers --limit=500
```

### Individual Sync
Sync a specific client programmatically:
```php
$client = Client::where('email', 'john@example.com')->first();
$result = app(QuickBooksService::class)->syncClientWithQuickBooks($client);
```

## Workflow Integration

### Invoice Creation Flow
1. Admin creates invoice for a client
2. System checks if client has `quickbooks_customer_id`
3. If not, automatically syncs client with QuickBooks
4. Uses QB customer ID for invoice creation

### New Client Flow
1. New client signs up or is manually added
2. System checks QuickBooks for matching email
3. If exists, links to existing QB customer
4. If not, creates new QB customer on first invoice

## Error Handling

### Common Issues

**Not Connected to QuickBooks**
- Error: "Not connected to QuickBooks"
- Solution: Connect at `/admin/quickbooks/connect`

**No Email Address**
- Customers without emails are automatically skipped
- Check QuickBooks records for missing emails

**Import Failed**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify QuickBooks API connection
- Check token expiration

### Logging

All import errors are logged with context:
```php
Log::error('Failed to import QuickBooks customer', [
    'customer_id' => 'QB-123',
    'error' => $exception->getMessage()
]);
```

## API Rate Limits

QuickBooks has API rate limits:
- **Sandbox**: 100 requests/minute
- **Production**: 500 requests/minute

The import handles this by:
- Batching requests efficiently
- Using single query to fetch customers
- Minimal API calls per customer

## Best Practices

1. **Run imports during low-traffic periods**
2. **Use `--sync` mode for existing databases**
3. **Start with `--limit` to test the import**
4. **Monitor logs for any errors**
5. **Verify import results before proceeding**

## Future Enhancements

Planned features:
- [ ] Scheduled automatic syncing (cron job)
- [ ] Webhook support for real-time updates
- [ ] Export clients to QuickBooks
- [ ] Bulk update customer data
- [ ] Two-way address sync
- [ ] Custom field mapping

## Related Documentation

- [QUICKBOOKS_BILLING.md](./QUICKBOOKS_BILLING.md) - Invoice and billing integration
- [ASSISTANT_GUIDE.md](./ASSISTANT_GUIDE.md) - AI assistant guide

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Verify QB connection in dashboard
- Test connection with "Test Connection" button
- Review QuickBooks API documentation
