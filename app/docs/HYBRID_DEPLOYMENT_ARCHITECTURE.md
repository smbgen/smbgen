# Hybrid Deployment Architecture

## Overview

CLIENTBRIDGE uses a **hybrid multi-tenancy model** that allows you to offer both cost-effective shared hosting and premium dedicated instances. This architecture maximizes profitability while providing flexibility for different customer tiers.

---

## Deployment Models

### 1. Shared Instance (Path-Based Tenancy)

**For: Starter, Professional, and Standard tier customers**

#### Configuration
```env
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=clientbridge.app
APP_URL=https://clientbridge.app
```

#### Access Pattern
- **Tenant Homepage**: `clientbridge.app/acme-consulting/`
- **Tenant Admin**: `clientbridge.app/acme-consulting/admin/dashboard`
- **Tenant Login**: `clientbridge.app/acme-consulting/login`
- **Super Admin**: `clientbridge.app/super-admin` (your control panel)

#### Characteristics
- ✅ Single Laravel Cloud instance serves all tenants
- ✅ Separate database per tenant (isolated data)
- ✅ Shared application resources (cost-effective)
- ✅ Easy to provision new tenants (no new server needed)
- ✅ Automatic updates for all tenants at once
- ❌ URL includes `/tenant-slug/` (less white-label)
- ❌ Shared server resources (potential noisy neighbor)

#### Pricing Strategy
- **Starter**: $29/mo (limited features, 1 user, 100 bookings/mo)
- **Professional**: $79/mo (all features, 5 users, 1000 bookings/mo)
- **Standard**: $149/mo (all features, unlimited users, unlimited bookings)

#### Cost Analysis
- **Single Laravel Cloud instance**: ~$50-200/mo depending on resources
- **Database**: ~$25-100/mo for all tenant databases
- **Gross margin**: 70-90% after infrastructure costs

---

### 2. Dedicated Instance (Single-Tenant Deployment)

**For: Enterprise and Premium white-label customers**

#### Configuration
```env
TENANCY_ENABLED=false
APP_URL=https://customerdomain.com
COMPANY_NAME=Customer Company Name
```

#### Access Pattern
- **Customer Homepage**: `customerdomain.com/`
- **Customer Admin**: `customerdomain.com/admin/dashboard`
- **Customer Login**: `customerdomain.com/login`
- **No super admin** (customer owns the instance)

#### Characteristics
- ✅ Completely isolated Laravel Cloud instance
- ✅ Customer's own domain (full white-label)
- ✅ Dedicated resources (better performance)
- ✅ Custom branding and configuration
- ✅ Can be customized per customer
- ✅ Enterprise credibility
- ❌ Requires new Laravel Cloud instance per customer
- ❌ Higher infrastructure costs
- ❌ Updates must be deployed per customer

#### Pricing Strategy
- **Enterprise**: $299/mo (dedicated instance, premium support, custom branding)
- **White-Label**: $499/mo (resellers who want to rebrand entirely)
- **Custom**: $999+/mo (custom development, integrations, SLA guarantees)

#### Cost Analysis
- **Laravel Cloud instance**: ~$50-200/mo per customer
- **Database**: ~$25-100/mo per customer
- **Total cost per customer**: ~$75-300/mo
- **Gross margin**: 50-85% depending on pricing tier

---

## When to Use Each Model

### Use Shared Instance (Path-Based) When:
- Customer budget is under $200/mo
- Customer doesn't require white-label branding
- Quick onboarding is important (no DNS setup)
- Customer is trying the product (trials)
- You want to minimize infrastructure management

### Use Dedicated Instance When:
- Customer pays $299+/mo
- Customer requires custom domain (full white-label)
- Customer needs guaranteed performance/resources
- Customer has compliance requirements (data isolation)
- Customer wants custom features/integrations
- Agency/reseller wants to rebrand the platform

---

## Provisioning Process

### Provisioning a Shared Tenant

1. **Super Admin Action** (in central app):
   ```
   Navigate to: clientbridge.app/super-admin/tenants
   Click: "Create New Tenant"
   Fill in: Tenant slug, company name, admin email
   Select: Subscription tier (Starter, Professional, Standard)
   ```

2. **Automatic Provisioning**:
   - Creates new tenant record in central database
   - Creates isolated tenant database (SQLite or MySQL)
   - Runs tenant migrations
   - Creates default admin user
   - Sends welcome email

3. **Customer Access**:
   ```
   URL: clientbridge.app/{tenant-slug}/
   Login: As provided by admin
   ```

4. **Ongoing Management**:
   - Monitor from super admin dashboard
   - Manage subscriptions/billing through Stripe
   - Suspend/activate tenants as needed
   - View usage analytics

---

### Provisioning a Dedicated Instance

1. **Sales Process**:
   - Customer signs Enterprise/White-Label contract
   - Customer provides domain name (e.g., `theircompany.com`)
   - Customer completes payment (often annual upfront)

2. **Create New Laravel Cloud Project**:
   ```bash
   # In Laravel Cloud dashboard:
   1. Create new project: "ClientBridge - CustomerName"
   2. Connect GitHub repo (same codebase)
   3. Set environment variables:
      - TENANCY_ENABLED=false
      - APP_URL=https://customerdomain.com
      - COMPANY_NAME=Customer Company Name
      - Configure Stripe, email, etc.
   4. Deploy
   ```

3. **DNS Configuration**:
   ```
   Provide customer with DNS instructions:
   Type: CNAME
   Name: www (or @)
   Value: [their-laravel-cloud-url].laravel.cloud
   ```

4. **Custom Domain Setup**:
   ```bash
   # In Laravel Cloud:
   1. Navigate to project settings
   2. Add custom domain: customerdomain.com
   3. Verify DNS
   4. SSL certificate auto-provisions
   ```

5. **Customer Handoff**:
   - Create admin user for customer
   - Provide login credentials
   - Send onboarding documentation
   - Schedule training call (if included in plan)

6. **Ongoing Management**:
   - Deploy updates when requested (or on schedule)
   - Monitor performance metrics
   - Provide premium support
   - Custom development if contracted

---

## Architectural Comparison

| Feature | Shared Instance | Dedicated Instance |
|---------|----------------|-------------------|
| **Tenant Isolation** | Database-level | Server-level |
| **URL Structure** | `/tenant-slug/` prefix | Clean root domain |
| **Provisioning Time** | Instant (click button) | 15-30 minutes |
| **Infrastructure Cost** | $1-5/mo per tenant | $75-300/mo per customer |
| **Performance** | Shared resources | Dedicated resources |
| **Branding** | Partial (colors, logo) | Complete white-label |
| **Updates** | Automatic, all at once | Controlled, per customer |
| **Customization** | Limited (same codebase) | Full (can fork code) |
| **Best For** | Small businesses | Enterprises, agencies |

---

## Revenue & Profitability

### Example Scenario: 100 Customers

#### Shared Instance Revenue:
- 50 Starter @ $29 = $1,450/mo
- 35 Professional @ $79 = $2,765/mo
- 15 Standard @ $149 = $2,235/mo
- **Total**: $6,450/mo ($77,400/year)

**Costs**:
- Laravel Cloud (1 large instance): $200/mo
- Database: $100/mo
- CDN/Storage: $50/mo
- **Total**: $350/mo ($4,200/year)

**Profit**: $73,200/year (94.6% gross margin)

---

#### Dedicated Instance Revenue:
- 5 Enterprise @ $299 = $1,495/mo
- 2 White-Label @ $499 = $998/mo
- 1 Custom @ $999 = $999/mo
- **Total**: $3,492/mo ($41,904/year)

**Costs**:
- 8 Laravel Cloud instances @ $150 = $1,200/mo
- 8 Databases @ $50 = $400/mo
- **Total**: $1,600/mo ($19,200/year)

**Profit**: $22,704/year (54.2% gross margin)

---

#### Combined Revenue:
- **Monthly**: $9,942
- **Annual**: $119,304
- **Blended Gross Margin**: 80.3%

---

## Migration Paths

### Upgrading: Shared → Dedicated

When a shared customer wants to upgrade to dedicated:

1. **Provision** dedicated Laravel Cloud instance
2. **Export** tenant database from shared instance
3. **Import** database to dedicated instance
4. **Update** tenant DNS to point to new instance
5. **Test** thoroughly
6. **Cutover** during maintenance window
7. **Archive** tenant data from shared instance

**Downtime**: ~15-30 minutes with proper planning

---

### Downgrading: Dedicated → Shared

Rarely needed, but possible:

1. **Create** new tenant in shared instance
2. **Export** database from dedicated instance
3. **Import** database to shared tenant database
4. **Update** DNS to point back to shared instance
5. **Decommission** dedicated Laravel Cloud instance

---

## Technical Implementation Notes

### Shared Instance (Path-Based Tenancy)

The `InitializeTenancyByPath` middleware automatically:
- Extracts tenant ID from URL path (e.g., `/acme-consulting/` → `acme-consulting`)
- Looks up tenant in central database
- Switches database connection to tenant's database
- All subsequent queries are scoped to that tenant

#### Route Structure:
```php
// Central routes (no tenant prefix)
Route::get('/super-admin', ...);
Route::get('/trial', ...);

// Tenant routes (automatically prefixed with /{tenant}/)
Route::middleware('tenant')->group(function () {
    Route::get('/', ...);  // Becomes: /acme-consulting/
    Route::get('/admin/dashboard', ...);  // Becomes: /acme-consulting/admin/dashboard
});
```

---

### Dedicated Instance (Single-Tenant)

When `TENANCY_ENABLED=false`:
- No tenant middleware applied
- Direct database connection (no switching)
- All routes at root level
- Simpler, faster execution
- No super admin panel

---

## Best Practices

### For Shared Instances:
1. **Set resource limits** per tenant (storage, API calls, users)
2. **Monitor performance** and upgrade infrastructure proactively
3. **Rate limit** expensive operations per tenant
4. **Implement** tenant-scoped caching
5. **Regular backups** of all tenant databases
6. **Separate** file storage per tenant (already implemented)

### For Dedicated Instances:
1. **Document** custom changes per customer
2. **Version control** customer-specific branches if needed
3. **Test updates** before deploying to customers
4. **Schedule** maintenance windows with customers
5. **Monitor** instance health and send alerts
6. **Backup** strategy per customer (they may have compliance needs)

---

## Conclusion

This hybrid architecture allows you to:
- **Maximize profit** with shared instances for price-sensitive customers
- **Capture premium revenue** with dedicated instances for enterprise customers
- **Scale efficiently** by adding tenants to existing shared infrastructure
- **Provide flexibility** to customers as their needs grow

The path-based tenancy model eliminates the complexity of wildcard DNS while maintaining strong tenant isolation, making it perfect for Laravel Cloud deployment.
