# SMBGen Documentation

This folder is organized by topic so active documentation stays easy to scan.

## Start Here

- New contributor: [guides/DEVELOPER_CONTRIBUTOR_START_HERE.md](guides/DEVELOPER_CONTRIBUTOR_START_HERE.md)
- Local environment planning: [planning/DEVELOPMENT_SETUP.md](planning/DEVELOPMENT_SETUP.md)
- Environment variables: [reference/ENV_EXAMPLE.md](reference/ENV_EXAMPLE.md)
- Known issues: [reference/REMAINING_ISSUES.md](reference/REMAINING_ISSUES.md)

---

## Folder Map

### guides/
- [Developer Setup](guides/DEVELOPER_CONTRIBUTOR_START_HERE.md)
- [Domain Connection Guide](guides/DOMAIN_CONNECTION_GUIDE.md)
- [Email Verification Testing](guides/EMAIL_VERIFICATION_TESTING.md)
- [Feature Flags](guides/FEATURE_FLAGS.md)
- [Dark Mode Guide](guides/DARK_MODE_GUIDE.md)
- [User Seeders](guides/USER_SEEDERS.md)

### architecture/
- [Hybrid Deployment Architecture](architecture/HYBRID_DEPLOYMENT_ARCHITECTURE.md)
- [Module Product Stacks](architecture/MODULE_PRODUCT_STACKS.md)
- [Theme System](architecture/THEME_SYSTEM.md)
- [Header Concerns](architecture/HEADER_CONCERNS_IMPLEMENTATION.md)
- [Tenant Admin Role Spec](architecture/tenant-admin-role-spec.md)

### features/
- [AI Implementation](features/AI_IMPLEMENTATION_COMPLETE.md)
- [Blog Implementation Summary](features/BLOG_IMPLEMENTATION_SUMMARY.md)
- [Booking Custom Fields](features/BOOKING_CUSTOM_FIELDS_IMPLEMENTATION.md)
- [CMS Company Colors](features/CMS_COMPANY_COLORS.md)

### billing/
- [Stripe Subscription Setup](billing/STRIPE_SUBSCRIPTION_SETUP.md)
- [Stripe Enhancements](billing/STRIPE_ENHANCEMENTS.md)
- [Subscription Implementation Summary](billing/SUBSCRIPTION_IMPLEMENTATION_SUMMARY.md)
- [Subscription Tier System](billing/SUBSCRIPTION_TIER_SYSTEM.md)

### integrations/
- [Google Calendar Debug Quickref](integrations/GOOGLE_CALENDAR_DEBUG_QUICKREF.md)
- [Google Calendar Debug Guide](integrations/GOOGLE_CALENDAR_DEBUG_GUIDE.md)

### multi-tenancy/
- [Multi-Tenant Setup](multi-tenancy/MULTI_TENANT_SETUP.md)
- [Implementation Status](multi-tenancy/MULTI_TENANCY_IMPLEMENTATION_STATUS.md)
- [Quick Reference](multi-tenancy/MULTI_TENANT_QUICKREF_SMBGENCOM.md)
- [Super Admin Setup](multi-tenancy/SUPER_ADMIN_SETUP.md)

### reference/
- [Environment Reference](reference/ENV_EXAMPLE.md)
- [Git Sync Strategy](reference/git-sync-strategy.md)
- [Remaining Issues](reference/REMAINING_ISSUES.md)

### planning/
- [Development Setup](planning/DEVELOPMENT_SETUP.md)
- [Multi-Tenancy Implementation Plan](planning/MULTI_TENANCY_IMPLEMENTATION.md)
- [Pricing Model](planning/PRICING_MODEL.md)
- [Technical Decisions Checklist](planning/TECHNICAL_DECISIONS_CHECKLIST.md)
- [Planning Contribution Notes](planning/CONTRIBUTING.md)

### archive/
Historical and superseded material kept for reference only.

---

## Quick Commands

**Enable a feature:**
```env
FEATURE_BLOG=true
FEATURE_BOOKING=true
```
Then run `php artisan optimize:clear`.

**Start development:**
```bash
npm run dev
php artisan serve
```

**Run tests:**
```bash
php artisan test
```

---

## Notes

- Active docs now live in topic folders instead of the docs root.
- `archive/` is intentionally noisy and historical.
- If you add a new active guide, place it in the closest topic folder and update this index.
