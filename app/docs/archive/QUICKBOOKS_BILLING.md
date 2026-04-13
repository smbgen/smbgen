# QuickBooks Billing Integration

## Overview

ClientBridge uses **QuickBooks Online** for invoice generation and payment processing. This replaces the previous Stripe integration and provides a complete invoicing and accounting solution.

## Features

- ✅ **Invoice Management**: Create and manage invoices directly in Laravel
- ✅ **QuickBooks Sync**: Push invoices to QuickBooks Online with one click
- ✅ **Customer Auto-Creation**: Automatically find or create customers in QuickBooks
- ✅ **Payment Links**: Generate secure QuickBooks payment URLs for customers
- ✅ **Email Integration**: Send payment request emails with QuickBooks payment links
- ✅ **Sync Status Tracking**: Track which invoices are synced with QuickBooks
- ✅ **OAuth2 Authentication**: Secure connection using QuickBooks OAuth2

## Configuration

### Environment Variables

Add these to your `.env` file:

```bash
# Feature Flag (disable billing if not needed)
FEATURE_BILLING=false

# QuickBooks OAuth Credentials
QUICKBOOKS_CLIENT_ID=your-client-id-here
QUICKBOOKS_CLIENT_SECRET=your-client-secret-here
QUICKBOOKS_REDIRECT_URI="${APP_URL}/admin/quickbooks/callback"
QUICKBOOKS_ENVIRONMENT=sandbox  # or 'production'
```

### QuickBooks App Setup

1. Go to [QuickBooks Developer Portal](https://developer.intuit.com/)
2. Create a new app or use an existing one
3. Get your **Client ID** and **Client Secret**
4. Add redirect URI: `https://yourdomain.com/admin/quickbooks/callback`
5. Enable **Accounting** scope
6. Copy credentials to your `.env` file

## Database Schema

QuickBooks integration adds these fields to the `invoices` table:

```php
$table->string('quickbooks_invoice_id')->nullable();
$table->string('quickbooks_customer_id')->nullable();
$table->text('quickbooks_invoice_url')->nullable();
$table->timestamp('quickbooks_synced_at')->nullable();
```

## Workflow

### 1. Connect QuickBooks

1. Navigate to **Admin → Billing & Invoices**
2. Click **Connect QuickBooks** in the integration panel
3. Authorize the application with your QuickBooks account
4. Connection status will show as "Connected" with company info

### 2. Create Invoice

1. Go to a client's profile or billing page
2. Click **New Invoice**
3. Add line items (description, quantity, unit price)
4. Add optional memo and due date
5. Click **Create Invoice**

### 3. Sync to QuickBooks

**Option A: Sync Only**
- Click **Sync QB** button on the invoice
- System will:
  - Find or create customer in QuickBooks by email
  - Create invoice with all line items
  - Generate public payment URL
  - Update local invoice with QuickBooks data

**Option B: Sync and Send**
- Click **Sync & Send** button on the invoice
- System will:
  - Perform all sync operations above
  - Send payment request email to customer
  - Email includes QuickBooks payment link
  - Customer can pay directly in QuickBooks

### 4. Customer Payment

1. Customer receives email with payment link
2. Clicks "Pay Invoice via QuickBooks"
3. Redirected to secure QuickBooks payment page
4. Makes payment using credit card or ACH
5. QuickBooks processes payment and updates invoice status

## Service Layer

### QuickBooksService Methods

```php
// Check connection status
$service->isConnected(?User $user = null): bool

// Get company information
$service->getCompanyInfo(?User $user = null): ?array

// Find or create customer by email
$service->findOrCreateCustomer(User $customer, ?User $authUser = null): ?array

// Sync Laravel invoice to QuickBooks
$service->syncInvoice(Invoice $invoice, ?User $authUser = null): array

// Get public payment URL
$service->getInvoiceUrl(string $invoiceId, ?User $user = null): ?string

// Test connection
$service->testConnection(?User $user = null): array
```

## Controller Actions

### AdminBillingController

```php
// Display all invoices with QuickBooks status
public function index()

// Show client's invoices
public function show(User $user)

// Create new invoice
public function create(User $user)
public function store(User $user, Request $request)

// Send invoice email (no QuickBooks sync)
public function sendInvoice(Invoice $invoice)

// Sync invoice to QuickBooks only
public function syncToQuickBooks(Invoice $invoice, QuickBooksService $service)

// Sync to QuickBooks AND send payment email
public function syncAndSendInvoice(Invoice $invoice, QuickBooksService $service)
```

## Routes

### Admin Routes
```php
// Billing Management
GET  /admin/billing                          - List all invoices
GET  /admin/billing/{user}                   - Show client invoices
GET  /admin/billing/{user}/create            - Create invoice form
POST /admin/billing/{user}                   - Store new invoice

// Invoice Actions
POST /admin/billing/invoices/{invoice}/send               - Send email only
POST /admin/billing/invoices/{invoice}/sync-quickbooks    - Sync to QB only
POST /admin/billing/invoices/{invoice}/sync-and-send      - Sync & send email

// QuickBooks Connection
GET  /admin/quickbooks/connect               - Initiate OAuth flow
GET  /admin/quickbooks/callback              - OAuth callback
POST /admin/quickbooks/disconnect            - Disconnect account
POST /admin/quickbooks/test                  - Test connection
```

### Client Routes
```php
GET  /billing                                - Client's invoices
POST /billing/invoices/{invoice}/pay         - Payment action
```

## Email Templates

Invoice emails automatically include QuickBooks payment links when available:

```blade
@if($invoice->hasQuickBooksPaymentUrl())
    <a href="{{ $invoice->quickbooks_invoice_url }}" class="btn">
        Pay Invoice via QuickBooks
    </a>
@else
    <a href="{{ route('billing.index') }}" class="btn">
        View Invoice
    </a>
@endif
```

## UI Components

### Dashboard Card
Shows QuickBooks connection status and links to billing page:
```blade
<x-dashboard.quickbooks-integration :data="$quickBooksData" />
```

### Billing Index
- Shows all invoices with QuickBooks sync status
- "QB Status" column indicates if invoice is synced
- QuickBooks integration panel at top of page

### Invoice Table Columns
- `#` - Invoice ID
- `Client` - Customer name (links to their invoices)
- `Date` - Invoice creation date
- `Status` - Invoice status (draft, sent, paid, void)
- `QB Status` - Sync status (Synced/Not Synced with checkmark icon)
- `Total` - Invoice total amount

## Token Management

QuickBooks OAuth tokens are stored on the `users` table:

```php
'quickbooks_realm_id'           // Company/Realm ID
'quickbooks_access_token'       // Short-lived access token
'quickbooks_refresh_token'      // Long-lived refresh token
'quickbooks_token_expires_at'   // Token expiration timestamp
```

Tokens are automatically refreshed when needed (within 5 minutes of expiration).

## Error Handling

The service includes comprehensive error handling:

- Invalid credentials → Returns error message
- Customer not found → Auto-creates new customer
- Network issues → Logs error and returns user-friendly message
- Token expired → Automatically refreshes and retries
- Sync failures → Displays specific error to admin

## Security

- **OAuth2 Flow**: Industry-standard authentication
- **CSRF Protection**: State parameter validates callback
- **Token Encryption**: Tokens stored securely in database
- **Scoped Access**: Only requests necessary permissions
- **HTTPS Required**: All API calls over secure connection

## Testing

### Sandbox Environment

Use QuickBooks Sandbox for testing:

```bash
QUICKBOOKS_ENVIRONMENT=sandbox
```

Sandbox features:
- Separate test company data
- Test payment processing
- No real money transactions
- Full API functionality

### Test Connection

1. Go to **Admin → Billing & Invoices**
2. Click **Test Connection** in QuickBooks panel
3. System will fetch company info and verify API access

## Troubleshooting

### Connection Issues

**Problem**: "Not connected to QuickBooks"
- Solution: Navigate to `/admin/quickbooks/connect` and re-authorize

**Problem**: "Failed to fetch company information"
- Check `quickbooks_refresh_token` is not null in database
- Verify credentials in `.env` file
- Ensure `QUICKBOOKS_ENVIRONMENT` matches your app setup

### Sync Issues

**Problem**: "Failed to find or create customer"
- Verify customer has valid email address
- Check QuickBooks API rate limits
- Review Laravel logs for detailed error

**Problem**: Invoice synced but no payment URL
- Verify `quickbooks_invoice_id` is set
- Check invoice was created successfully in QuickBooks
- Ensure realm_id is correct

## Best Practices

1. **Always test in sandbox** before going to production
2. **Sync before sending** - Use "Sync & Send" for best workflow
3. **Monitor sync status** - Check "QB Status" column regularly
4. **Handle disconnections** - Re-authorize promptly if connection lost
5. **Log everything** - Review Laravel logs for debugging
6. **Backup data** - QuickBooks is source of truth for accounting

## Migration from Stripe

Previous Stripe integration has been disabled. All payment routes now use QuickBooks:

- ❌ `POST /payment/checkout` - Removed
- ❌ `GET /payment/success` - Removed
- ❌ `GET /payment/cancel` - Removed
- ❌ `GET /payment-test` - Removed
- ✅ QuickBooks payment URLs via email

## Future Enhancements

Planned features:
- [ ] Webhook support for payment notifications
- [ ] Automatic status updates from QuickBooks
- [ ] Invoice status sync (paid/unpaid)
- [ ] Batch invoice creation
- [ ] Custom invoice templates
- [ ] Recurring invoices
- [ ] Payment reminders

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Review QuickBooks API logs in developer portal
- Test connection using built-in test tool
- Verify environment variables are correct

## API Documentation

Official QuickBooks API docs:
- [QuickBooks API Explorer](https://developer.intuit.com/app/developer/qbo/docs/api/accounting/all-entities/invoice)
- [OAuth 2.0 Guide](https://developer.intuit.com/app/developer/qbo/docs/develop/authentication-and-authorization/oauth-2.0)
- [PHP SDK Documentation](https://github.com/intuit/QuickBooks-V3-PHP-SDK)
