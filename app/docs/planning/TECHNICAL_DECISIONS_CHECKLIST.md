# Technical Decisions Checklist
**ClientBridge Multi-Tenancy Implementation**

**Purpose:** Critical decisions needed before starting implementation  
**Status:** 🟡 Awaiting Decisions  
**Deadline:** Before Phase 1 starts

---

## How to Use This Checklist

- [ ] Review each decision point
- [ ] Discuss with team/stakeholders
- [ ] Mark decision made: ✅
- [ ] Document rationale in notes
- [ ] Update implementation plan with decisions

---

## 1. Database Strategy

### Decision: Single Database vs Separate Databases Per Tenant

**Current Config:** Stancl/tenancy configured for separate databases

**Option A: Single Shared Database**
- All tenants in one database with `tenant_id` column
- ✅ Simpler queries and migrations
- ✅ Easier backups and maintenance
- ✅ Better resource utilization
- ❌ Risk of data leaks if query misses `tenant_id`
- ❌ Harder to scale individual tenants
- ❌ Compliance concerns (HIPAA, SOC2)

**Option B: Separate Database Per Tenant** (Current)
- Each tenant gets `tenant_{id}` database
- ✅ Complete data isolation (security)
- ✅ Per-tenant backups/exports
- ✅ Easier compliance (HIPAA, SOC2)
- ✅ Can scale individual tenants
- ❌ More complex migrations
- ❌ Database connection overhead
- ❌ Higher infrastructure costs

**Option C: Hybrid**
- Shared database for Starter plan
- Separate databases for Pro/Enterprise
- ⚖️ Balances cost and features

### 🔲 Decision Needed

- [ ] **Option A** - Single shared database (simpler MVP)
- [ ] **Option B** - Separate databases (current config, more secure)
- [ ] **Option C** - Hybrid approach (complex but flexible)

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:




```

---

## 2. Production Database Engine

### Decision: SQLite vs MySQL vs PostgreSQL

**Current:** SQLite (local), MySQL (production via Laravel Cloud)

**For Multi-Tenant Production:**
- ❌ **SQLite** - Not suitable for multi-tenant (file-based, locking issues)
- ✅ **MySQL** - Currently used, good Laravel support
- ✅ **PostgreSQL** - Better performance, schema support, JSON queries

### 🔲 Decision Needed

- [ ] **MySQL** - Stick with current setup
- [ ] **PostgreSQL** - Migrate for better performance
- [ ] **Other** (specify): _________________

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:




```

---

## 3. Environment Strategy

### Decision: Staging Environment Setup

**Current Problem:** Testing in production (risky!)

**Proposed:**
- **Local:** Herd + SQLite
- **Staging:** Laravel Cloud staging environment + MySQL
- **Production:** Laravel Cloud production + MySQL

### 🔲 Decision Needed

- [ ] **Set up staging environment** before Phase 3
- [ ] **Use Laravel Cloud staging** (recommended)
- [ ] **Use separate VPS for staging** (more control)
- [ ] **Continue testing in production** (NOT recommended)

**Action Items:**
- [ ] Create staging environment in Laravel Cloud
- [ ] Configure staging environment variables
- [ ] Set up staging database
- [ ] Configure GitHub to auto-deploy `staging` branch
- [ ] Document staging access for team

**Notes:**
```
Decision maker: _________________
Date: _________________
Staging URL: https://staging.clientbridge.app
```

---

## 4. Domain Strategy

### Decision: Subdomains Only vs Custom Domains

**Option A: Subdomains Only (MVP)**
- `tenant.clientbridge.app`
- ✅ Simple DNS management
- ✅ Easy SSL (wildcard cert)
- ✅ Fast implementation
- ❌ Less professional for customers

**Option B: Custom Domains (Phase 2)**
- `app.acmecorp.com`
- ✅ More professional
- ✅ White-label capability
- ❌ DNS verification required
- ❌ SSL certificate management
- ❌ Higher complexity

### 🔲 Decision Needed

- [ ] **MVP:** Subdomains only (add custom domains post-launch)
- [ ] **Launch:** Support both from day 1

**If custom domains supported:**
- [ ] Limit to Enterprise plan only
- [ ] Charge extra fee ($10/month per domain)
- [ ] Include in Enterprise plan

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 5. Google OAuth Multi-Tenant Strategy

### Decision: OAuth Redirect Handling

**Problem:** Google doesn't support wildcard redirect URIs (`*.clientbridge.app`)

**Option A: Central Auth Proxy** (Recommended for MVP)
- Central auth at `auth.clientbridge.app`
- Flow: `tenant.app` → `auth.app` → Google → `auth.app` → `tenant.app`
- ✅ Single Google OAuth app
- ✅ Simple configuration
- ❌ Extra redirect hop

**Option B: Per-Tenant OAuth Apps**
- Each tenant registers own Google OAuth app
- ✅ Clean direct redirects
- ❌ Complex tenant setup
- ❌ Not self-service friendly
- ❌ User friction

**Option C: Central Login Page**
- All users log in at `app.clientbridge.app`
- After auth, redirect to tenant subdomain
- ✅ Single OAuth app
- ❌ Less intuitive UX

### 🔲 Decision Needed

- [ ] **Option A** - Central auth proxy (recommended)
- [ ] **Option B** - Per-tenant OAuth apps
- [ ] **Option C** - Central login page

**Action Items if Option A:**
- [ ] Create `auth.clientbridge.app` subdomain
- [ ] Build OAuth proxy controller
- [ ] Test redirect flow
- [ ] Update Google OAuth settings

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 6. Existing Production Data Migration

### Decision: What to do with current production data?

**Current:** Production has users, clients, bookings with NO `tenant_id`

**Option A: Migrate to "Legacy" Tenant** (Recommended)
- Create default tenant: "Legacy Account"
- Assign all existing data to this tenant
- ✅ Preserves all data
- ✅ Allows gradual migration
- ✅ Can continue serving existing customers
- ❌ May not reflect true multi-tenant structure

**Option B: Fresh Start**
- Multi-tenant mode requires fresh database
- ✅ Clean implementation
- ❌ Loses existing customer data
- ❌ Can't use for current customers

**Option C: Manual Tenant Assignment**
- Manually create tenants
- Manually assign users/clients to tenants
- ✅ Most accurate
- ❌ Very time-consuming
- ❌ Risk of errors

### 🔲 Decision Needed

- [ ] **Option A** - Migrate to legacy tenant (recommended)
- [ ] **Option B** - Fresh start
- [ ] **Option C** - Manual assignment

**Action Items if Option A:**
- [ ] Create migration script: `MigrateExistingDataToTenant.php`
- [ ] Create default tenant with Starter plan
- [ ] Assign all existing records to this tenant
- [ ] Test thoroughly on staging
- [ ] Document rollback procedure

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 7. Trial Strategy

### Decision: Trial Duration and Feature Access

**Trial Duration:**
- [ ] **7 days** (short, creates urgency)
- [ ] **14 days** (standard, recommended)
- [ ] **30 days** (generous, may reduce conversion)

**Trial Plan Access:**
- [ ] **Starter features** (basic trial)
- [ ] **Professional features** (recommended - show full value)
- [ ] **Enterprise features** (may be overkill)

**Credit Card Requirement:**
- [ ] **Required** (higher conversion, lower signup)
- [ ] **Not required** (recommended - lower friction)

**Trial Ending Behavior:**
- [ ] **Auto-convert to paid** (if CC on file)
- [ ] **Auto-downgrade to free tier** (if exists)
- [ ] **Suspend account** until payment (recommended)

### 🔲 Decision Needed

**Chosen Trial Strategy:**
```
Duration: ___ days
Features: _______________ plan
Credit card: Required / Not required
After trial: _______________
```

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 8. Feature Limit Enforcement

### Decision: How to handle limit violations?

**When tenant tries to create 6th user (limit is 5):**

**Option A: Hard Block** (Recommended)
- Prevent action entirely
- Show modal: "Upgrade to add more users"
- ✅ Forces upgrades
- ❌ Bad UX if unexpected

**Option B: Soft Limit + Grace Period**
- Allow overage
- Send warning email
- Enforce after 7 days
- ✅ Better UX
- ❌ More complex to implement
- ❌ Free usage during grace period

**Option C: Overage Billing**
- Allow overage
- Charge extra (e.g., $5 per extra user)
- ✅ Flexible for customers
- ❌ Requires usage tracking
- ❌ Complex billing logic

### 🔲 Decision Needed Per Limit Type

| Limit | Strategy | Grace Period | Notes |
|-------|----------|--------------|-------|
| **Max Users** | Hard block / Soft / Overage | ___ days | |
| **Max Clients** | Hard block / Soft / Overage | ___ days | |
| **Storage GB** | Hard block / Soft / Overage | ___ days | |
| **Bookings/Month** | Hard block / Soft / Overage | ___ days | |
| **Emails/Month** | Hard block / Soft / Overage | ___ days | |
| **CMS Pages** | Hard block / Soft / Overage | ___ days | |

**Recommendations:**
- **Users/Clients:** Hard block (immediate upgrade prompt)
- **Storage:** Soft limit with 7-day grace
- **Email/Bookings:** Soft limit, reset monthly

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 9. Subscription Lifecycle Policies

### Decision: Suspension, Cancellation, Deletion

**Payment Failure:**
- [ ] **Immediate suspension** (strict)
- [ ] **7-day grace period** (recommended)
- [ ] **30-day grace period** (generous)

**Cancellation:**
- [ ] **Immediate access termination**
- [ ] **Access until end of billing period** (recommended)

**Data Retention After Cancellation:**
- [ ] **30 days** (short)
- [ ] **90 days** (recommended)
- [ ] **1 year** (very generous)
- [ ] **Forever** (storage costs)

**Deletion Warning:**
- [ ] **7 days before deletion**
- [ ] **3 days before deletion**
- [ ] **No warning**

**Reactivation:**
- [ ] **One-click reactivation** during retention period (recommended)
- [ ] **Require contacting support**

### 🔲 Decision Needed

**Chosen Policies:**
```
Payment failure grace: ___ days
Cancellation access: Until end of period / Immediate
Data retention: ___ days
Deletion warning: ___ days before
Reactivation: Self-service / Support required
```

**Notes:**
```
Decision maker: _________________
Date: _________________
Rationale:



```

---

## 10. Pricing Model Final Approval

### Decision: Confirm Pricing Tiers

**Proposed Pricing (from PRICING_MODEL.md):**

| Plan | Monthly | Yearly | Users | Clients | Storage |
|------|---------|--------|-------|---------|---------|
| Starter | $29 | $290 | 3 | 100 | 5 GB |
| Professional | $99 | $990 | 15 | 1,000 | 50 GB |
| Enterprise | $299 | $2,990 | 100 | 10,000 | 250 GB |

### 🔲 Decision Needed

- [ ] **Approve pricing as proposed**
- [ ] **Modify pricing** (specify below)
- [ ] **Conduct competitor analysis first**

**Modifications (if any):**
```
Starter: $___ /month
Professional: $___ /month
Enterprise: $___ /month

Rationale:



```

**Annual Discount:**
- [ ] **17% discount** (as proposed)
- [ ] **Other:** ___% discount

**Notes:**
```
Decision maker: _________________
Date: _________________
Approved by: _________________
```

---

## 11. Feature-to-Plan Mapping

### Decision: Confirm which features belong to which plans

**From PRICING_MODEL.md - Review and Approve:**

| Feature | Starter | Pro | Enterprise |
|---------|---------|-----|------------|
| Booking Management | ✅ | ✅ | ✅ |
| Billing (Basic) | ✅ | ✅ | ✅ |
| Messages | ✅ | ✅ | ✅ |
| File Management | ✅ | ✅ | ✅ |
| CMS | ❌ | ✅ | ✅ |
| Custom Branding | ❌ | ✅ | ✅ |
| Google Calendar | ❌ | ✅ | ✅ |
| Google Drive | ❌ | ✅ | ✅ |
| Inspection Reports | ❌ | ❌ | ✅ |
| Phone System | ❌ | ❌ | ✅ |
| API Access | ❌ | ❌ | ✅ |
| White-Label | ❌ | ❌ | ✅ |

### 🔲 Decision Needed

- [ ] **Approve feature mapping as proposed**
- [ ] **Modify feature mapping** (specify below)

**Modifications (if any):**
```
Move [feature] from [plan] to [plan]:


Rationale:



```

**Notes:**
```
Decision maker: _________________
Date: _________________
```

---

## 12. Super Admin Access

### Decision: Who gets super admin access?

**Super Admin Powers:**
- View/edit all tenants
- Impersonate any user
- Access all data
- Suspend/delete tenants
- Manage pricing plans

### 🔲 Decision Needed

**Initial Super Admins:**
- [ ] ___________________ (name/email)
- [ ] ___________________ (name/email)
- [ ] ___________________ (name/email)

**Super Admin Creation Process:**
- [ ] **Manual via Tinker** (secure but manual)
- [ ] **Artisan command** (easier but need to secure)
- [ ] **Super admin panel UI** (first super admin must create others)

**Super Admin Audit Logging:**
- [ ] **Log all actions** (recommended for compliance)
- [ ] **Log only sensitive actions** (deletions, impersonations)
- [ ] **No logging** (not recommended)

**Notes:**
```
Decision maker: _________________
Date: _________________
Security considerations:



```

---

## 13. Monitoring & Alerting

### Decision: What to monitor and alert on?

**System Health:**
- [ ] Server uptime
- [ ] Database performance
- [ ] Queue worker status
- [ ] Failed jobs

**Business Metrics:**
- [ ] New tenant signups
- [ ] Trial conversions
- [ ] Subscription cancellations
- [ ] Payment failures
- [ ] MRR changes

**Security:**
- [ ] Failed login attempts
- [ ] Super admin actions
- [ ] Suspicious API usage

**Alerting Channels:**
- [ ] Email
- [ ] Slack/Discord webhook
- [ ] SMS (critical only)
- [ ] PagerDuty (for production incidents)

### 🔲 Decision Needed

**Monitoring Tools:**
- [ ] Laravel Telescope (development)
- [ ] Laravel Pulse (production metrics)
- [ ] Sentry (error tracking)
- [ ] Custom dashboard

**Alert Recipients:**
```
Critical alerts: ___________________
Business alerts: ___________________
Security alerts: ___________________
```

**Notes:**
```
Decision maker: _________________
Date: _________________
```

---

## 14. Backup & Disaster Recovery

### Decision: Backup strategy

**Database Backups:**
- [ ] **Daily** automated backups
- [ ] **Hourly** automated backups (production)
- [ ] **Manual** before major deployments

**Backup Retention:**
- [ ] Keep last **7 days**
- [ ] Keep last **30 days** (recommended)
- [ ] Keep last **90 days**

**File Storage Backups:**
- [ ] **Same as database** schedule
- [ ] **Weekly** only (files change less often)
- [ ] **No backups** (rely on Google Drive integration)

**Disaster Recovery Plan:**
- [ ] **RTO (Recovery Time Objective):** ___ hours
- [ ] **RPO (Recovery Point Objective):** ___ hours
- [ ] **Tested recovery procedure:** Yes / No

### 🔲 Decision Needed

**Backup Strategy:**
```
Database: _______________ frequency
Retention: _______________ days
Files: _______________ frequency
RTO: _______________ hours
RPO: _______________ hours
```

**Laravel Cloud Auto-Backups:**
- [ ] Enabled (check Laravel Cloud settings)
- [ ] Need to configure

**Notes:**
```
Decision maker: _________________
Date: _________________
```

---

## 15. Testing Strategy

### Decision: Testing requirements before deployment

**Test Coverage Requirements:**
- [ ] **80% minimum** for critical paths
- [ ] **90% minimum** for payment/billing code
- [ ] **100% minimum** for security/auth code

**Test Types Required:**
- [ ] Unit tests for models/services
- [ ] Feature tests for controllers/routes
- [ ] Browser tests for critical flows
- [ ] Manual QA on staging

**CI/CD Integration:**
- [ ] **GitHub Actions** - run tests on every PR
- [ ] **Block merge** if tests fail
- [ ] **Block deploy** if tests fail

**Staging Testing Checklist:**
- [ ] Full tenant signup flow
- [ ] Plan upgrade/downgrade
- [ ] Payment success/failure
- [ ] Tenant data isolation
- [ ] Super admin impersonation
- [ ] Feature restrictions per plan

### 🔲 Decision Needed

**Testing Requirements:**
```
Minimum coverage: ___%
Required test types: ________________
CI/CD: GitHub Actions / Other: ________
Manual QA required: Yes / No
```

**Notes:**
```
Decision maker: _________________
Date: _________________
```

---

## 16. Documentation Requirements

### Decision: What documentation needs to be written?

**Technical Documentation:**
- [ ] Multi-tenancy architecture (✅ MULTI_TENANCY_IMPLEMENTATION.md exists)
- [ ] Development setup (✅ DEVELOPMENT_SETUP.md exists)
- [ ] Contribution guide (✅ CONTRIBUTING.md exists)
- [ ] API documentation (if building API)
- [ ] Deployment runbook

**User Documentation:**
- [ ] Tenant onboarding guide
- [ ] Admin user guide
- [ ] Feature usage guides
- [ ] Billing/subscription FAQ
- [ ] Support articles

**Business Documentation:**
- [ ] Pricing model (✅ PRICING_MODEL.md exists)
- [ ] Support policies
- [ ] SLA agreements (Enterprise)

### 🔲 Decision Needed

**Priority Documentation (Before Launch):**
- [ ] _______________________________
- [ ] _______________________________
- [ ] _______________________________

**Post-Launch Documentation:**
- [ ] _______________________________
- [ ] _______________________________

**Notes:**
```
Decision maker: _________________
Date: _________________
```

---

## 17. Launch Strategy

### Decision: Phased rollout vs full launch?

**Option A: Phased Rollout** (Recommended)
- **Phase 1:** Beta with 5-10 early customers
- **Phase 2:** Limited release to 50 customers
- **Phase 3:** Public launch
- ✅ Catch issues early
- ✅ Gather feedback
- ❌ Slower to market

**Option B: Full Public Launch**
- Announce broadly, accept all signups
- ✅ Faster time to market
- ❌ Higher risk

**Option C: Invite-Only Launch**
- Waitlist → invite codes
- ✅ Controlled growth
- ✅ Creates exclusivity/demand
- ❌ May lose customers to competitors

### 🔲 Decision Needed

- [ ] **Phased rollout** (recommended)
- [ ] **Full public launch**
- [ ] **Invite-only launch**

**If phased rollout:**
```
Beta phase: ___ customers, ___ weeks
Limited release: ___ customers, ___ weeks
Public launch: After ___ date
```

**Notes:**
```
Decision maker: _________________
Date: _________________
Marketing plan aligned: Yes / No
```

---

## Implementation Readiness Checklist

### Before Starting Phase 1

- [ ] All decisions above are marked ✅
- [ ] Decisions documented and communicated to team
- [ ] Staging environment is set up and accessible
- [ ] Stripe account configured (test mode)
- [ ] GitHub repository access for all contributors
- [ ] Development setup guide followed by all devs
- [ ] Project board created with Phase 1 tasks
- [ ] First sprint planned (2-week sprint recommended)

### Before Starting Phase 3 (Routing)

- [ ] Database schema completed and tested (Phase 1)
- [ ] Authentication & roles implemented (Phase 2)
- [ ] Google OAuth strategy decided and documented
- [ ] DNS configured for subdomain testing
- [ ] SSL certificates configured (wildcard cert)

### Before Starting Phase 5 (Billing)

- [ ] Stripe products/prices created
- [ ] Webhook endpoint configured
- [ ] Payment failure handling decided
- [ ] Subscription policies documented

### Before Production Deployment

- [ ] All tests passing (unit, feature, browser)
- [ ] Staging fully tested by QA
- [ ] Data migration script tested on staging
- [ ] Rollback procedure documented and tested
- [ ] Monitoring/alerting configured
- [ ] Backup strategy implemented
- [ ] Support team trained on new features
- [ ] User documentation published
- [ ] Marketing materials ready (if public launch)

---

## Decision Log

Track when decisions were made:

| Decision # | Topic | Decision | Date | By | Status |
|------------|-------|----------|------|----|----|
| 1 | Database Strategy | | | | ⏳ |
| 2 | Database Engine | | | | ⏳ |
| 3 | Staging Setup | | | | ⏳ |
| 4 | Domain Strategy | | | | ⏳ |
| 5 | OAuth Strategy | | | | ⏳ |
| 6 | Data Migration | | | | ⏳ |
| 7 | Trial Strategy | | | | ⏳ |
| 8 | Limit Enforcement | | | | ⏳ |
| 9 | Subscription Policies | | | | ⏳ |
| 10 | Pricing Approval | | | | ⏳ |
| 11 | Feature Mapping | | | | ⏳ |
| 12 | Super Admin Access | | | | ⏳ |
| 13 | Monitoring Setup | | | | ⏳ |
| 14 | Backup Strategy | | | | ⏳ |
| 15 | Testing Requirements | | | | ⏳ |
| 16 | Documentation Needs | | | | ⏳ |
| 17 | Launch Strategy | | | | ⏳ |

**Legend:** ⏳ Pending | ✅ Decided | 🔄 Revisit

---

## Next Steps

1. **Schedule decision meeting** with key stakeholders
2. **Review this checklist** section by section
3. **Make and document all decisions**
4. **Update** [MULTI_TENANCY_IMPLEMENTATION.md](MULTI_TENANCY_IMPLEMENTATION.md) with decisions
5. **Create implementation task board** in GitHub Projects
6. **Assign Phase 1 tasks** to developers
7. **Set sprint dates** and start building! 🚀

---

**Last Updated:** December 28, 2025  
**Status:** 🟡 Awaiting Decisions (0/17 complete)  
**Owner:** Product & Engineering Leadership
