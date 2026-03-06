# CLIENTBRIDGE Freemium Multi-Tenant Strategy

**Version:** 2.0  
**Date:** November 24, 2025  
**Status:** Strategic Pivot - Planning Phase  
**Inspired by:** Jason Gilmore conversation & Xenon Partners evaluation framework

---

## Executive Summary

**The Pivot:** Transform CLIENTBRIDGE from whitelabel single-tenant to freemium multi-tenant SaaS platform.

**Core Strategy:** Give away the booking system for free to maximize user adoption, then monetize through premium add-ons (email management, CMS, AI phone system).

**Target Market:** Service professionals (home inspectors, contractors, lawn care, HVAC, plumbers, electricians) who need appointment booking but can't justify $50-200/month for complex tools like Housecall Pro.

**Competitive Advantage:**
- Free tier includes full booking system (not just limited trial)
- Google Workspace native integration
- Simpler than enterprise tools
- More powerful than booking-only solutions
- Built by someone who worked in service business for 3 years

**Success Metrics:**
- 1,000 free tier users by Q1 2026
- $10K MRR by Q2 2026
- $50K MRR by Q4 2026

---

## The Freemium Model

### FREE TIER: "CLIENTBRIDGE Booking"

**What's Included:**
```
✅ Google OAuth signup (any Gmail account)
✅ Public booking page (clientbridge.app/book/username)
✅ Availability settings (hours, days, breaks)
✅ Google Calendar two-way sync
✅ Automatic Google Meet link generation
✅ Email confirmations (basic template)
✅ SMS reminders (via Twilio, limited quantity)
✅ Mobile-responsive booking widget
✅ Embed code for existing websites
✅ Up to 50 bookings/month
✅ Single user account
✅ Community support (Discord/forum)
```

**What's Limited:**
```
❌ Custom branding (CLIENTBRIDGE logo on booking page)
❌ Email management features
❌ CMS/landing pages
❌ AI phone system
❌ Advanced analytics
❌ Multiple team members
❌ API access
❌ White-label option
❌ Priority support
```

**Why This Works:**
1. **Removes friction:** No credit card required to start
2. **Solves real problem:** Eliminates phone tag for appointment booking
3. **Network effect:** Every booking page is marketing (powered by CLIENTBRIDGE)
4. **Qualification:** 50 bookings/month filters serious users from tire-kickers
5. **Upgrade path:** Users who hit 50 bookings/month are growing and can afford paid

### PAID ADD-ONS

#### 1. Email Management - $29/month
```
✅ Professional email composer
✅ Email templates library
✅ Multi-recipient support
✅ Email tracking (opens, clicks)
✅ Scheduled sending
✅ Email logs and history
✅ Automated follow-up sequences
✅ Unlimited emails
✅ Priority support
```

**Target User:** Service professionals who need to send professional emails, quotes, follow-ups but don't want to manage Gmail manually.

**Competitive Comparison:**
- Mailchimp: $13-300+/month (overkill for SMBs)
- HubSpot: $45-3200+/month (too complex)
- CLIENTBRIDGE: $29/month (just what you need)

#### 2. CMS & Lead Forms - $39/month
```
✅ Landing page builder
✅ Custom lead capture forms
✅ Form builder (drag-and-drop)
✅ Lead tracking and management
✅ Form submission notifications
✅ Property address capture
✅ Lead source tracking
✅ Lead-to-client conversion
✅ SEO optimization
✅ Custom domain support
✅ Priority support
```

**Target User:** Service professionals who need a website and lead capture but don't want to hire a web developer or pay $100+/month for website builders.

**Competitive Comparison:**
- Wix/Squarespace: $16-45/month (no CRM integration)
- Leadpages: $37-239/month (no booking integration)
- CLIENTBRIDGE: $39/month (website + forms + CRM + booking)

#### 3. AI Phone System (Bland.ai Integration) - $79/month
```
✅ AI-powered call answering
✅ Appointment booking via phone
✅ Customer inquiry handling
✅ Call transcription
✅ Emotion analysis
✅ Call routing rules
✅ Business hours enforcement
✅ After-hours messaging
✅ CRM auto-updates from calls
✅ SMS fallback
✅ 200 minutes included
✅ Priority support
```

**Target User:** Service professionals who lose business because they can't answer phone while on job sites.

**Competitive Comparison:**
- Bland.ai direct: $0.09/min ($18 for 200 min) + complexity
- Ruby Receptionists: $240-840/month (human receptionists)
- CLIENTBRIDGE: $79/month (AI + integration)

#### 4. Team Plan - $99/month
```
✅ Everything in free tier
✅ Up to 5 team members
✅ Shared calendar
✅ Team scheduling rules
✅ Round-robin booking
✅ Internal messaging
✅ Team analytics
✅ Unlimited bookings
✅ Priority support
```

**Target User:** Growing service businesses that need multiple people handling appointments.

#### 5. Pro Bundle - $149/month (Save $28)
```
✅ Everything: Booking + Email + CMS + AI Phone
✅ White-label option (remove CLIENTBRIDGE branding)
✅ Custom domain
✅ Advanced analytics
✅ API access
✅ Priority support
✅ Dedicated account manager (at scale)
```

**Target User:** Established service businesses that want the full platform.

### ENTERPRISE: Custom Pricing

**For larger organizations that need:**
- Multi-location support
- Advanced integrations
- Custom development
- Training and onboarding
- SLA guarantees
- Dedicated infrastructure

**Minimum:** $500/month

---

## Multi-Tenant Architecture

### Technical Design

#### Tenant Isolation Strategy: Shared Database with tenant_id

**Why this approach:**
- Simplest to implement initially
- Laravel Nova/Filament has great support
- Easier to manage migrations
- Lower infrastructure costs at scale
- Can migrate to schema-per-tenant later if needed

**Core Tables Structure:**
```sql
users
  - id
  - tenant_id (foreign key)
  - name
  - email
  - google_id
  - plan (enum: free, email, cms, ai, team, pro, enterprise)
  - subscription_status
  - stripe_customer_id
  
tenants
  - id
  - subdomain (unique)
  - custom_domain (nullable)
  - branding (json: logo, colors)
  - settings (json)
  - plan
  - trial_ends_at
  - subscription_ends_at
  
clients
  - id
  - tenant_id (indexed, foreign key)
  - name
  - email
  - phone
  - property_address
  
bookings
  - id
  - tenant_id (indexed, foreign key)
  - client_id
  - booking_date
  - booking_time
  - status
  
lead_forms
  - id
  - tenant_id (indexed, foreign key)
  - name
  - email
  - message
  - source
  
cms_pages
  - id
  - tenant_id (indexed, foreign key)
  - slug
  - title
  - content
```

**Global Scope Implementation:**
```php
// app/Models/Concerns/BelongsToTenant.php
trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && ! $model->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}

// Usage in models:
class Client extends Model
{
    use BelongsToTenant;
}
```

#### URL Structure

**Booking Pages:**
- Free tier: `clientbridge.app/book/{username}`
- Custom domain: `schedule.businessname.com`
- Subdomain: `businessname.clientbridge.app`

**Admin Dashboard:**
- All users: `app.clientbridge.app/admin`
- Tenant context determined by authentication

**Public CMS Pages:**
- Free/paid: `businessname.clientbridge.app/{slug}`
- Custom domain: `businessname.com/{slug}`

#### Signup Flow

```
1. User visits clientbridge.app
2. Clicks "Sign up free"
3. Google OAuth consent screen
4. Returns to clientbridge.app/auth/callback
5. Creates tenant record with random subdomain
6. Creates user record linked to tenant
7. Redirects to app.clientbridge.app/admin/onboarding
8. Onboarding wizard:
   - Choose username for booking page
   - Set availability
   - Connect Google Calendar
   - Test booking
9. Redirects to app.clientbridge.app/admin/dashboard
10. Shows "Your booking page: clientbridge.app/book/{username}"
```

#### Feature Flagging

```php
// config/plans.php
return [
    'free' => [
        'booking' => true,
        'email' => false,
        'cms' => false,
        'ai_phone' => false,
        'team' => false,
        'white_label' => false,
        'api' => false,
        'monthly_booking_limit' => 50,
        'team_members' => 1,
    ],
    'email' => [
        'booking' => true,
        'email' => true,
        'cms' => false,
        'ai_phone' => false,
        'team' => false,
        'white_label' => false,
        'api' => false,
        'monthly_booking_limit' => null,
        'team_members' => 1,
    ],
    // ... other plans
];

// Usage in code:
if (auth()->user()->canUseFeature('email')) {
    // Show email management
}

// Or in Blade:
@can('use-email')
    <a href="{{ route('admin.email.index') }}">Email Management</a>
@endcan
```

---

## Migration Strategy

### Phase 1: Core Multi-Tenant Foundation (Weeks 1-4)

**Week 1: Database Schema**
- [ ] Create `tenants` migration
- [ ] Add `tenant_id` to all existing tables
- [ ] Create global scope trait
- [ ] Add tenant seeder for testing
- [ ] Test tenant isolation thoroughly

**Week 2: Authentication & Signup**
- [ ] Build Google OAuth signup flow
- [ ] Create tenant on signup
- [ ] Generate unique subdomain
- [ ] Build onboarding wizard
- [ ] Add plan selection (default: free)

**Week 3: Booking System Adaptation**
- [ ] Public booking page per tenant
- [ ] Username-based routing
- [ ] Tenant-scoped availability
- [ ] Google Calendar per tenant
- [ ] Test booking flow end-to-end

**Week 4: Admin Dashboard**
- [ ] Tenant-aware dashboard
- [ ] Usage metrics (bookings this month)
- [ ] Upgrade prompts when near limit
- [ ] Settings page per tenant
- [ ] Help documentation

### Phase 2: Stripe Integration (Weeks 5-6)

**Week 5: Subscription Infrastructure**
- [ ] Stripe API integration
- [ ] Create subscription products in Stripe
- [ ] Webhook handling (subscription updates)
- [ ] Customer portal integration
- [ ] Invoice management

**Week 6: Upgrade Flows**
- [ ] In-app upgrade buttons
- [ ] Checkout flow for each add-on
- [ ] Downgrade/cancel flow
- [ ] Proration handling
- [ ] Payment method management

### Phase 3: Feature Add-Ons (Weeks 7-10)

**Week 7-8: Email Management Add-On**
- [ ] Feature flag implementation
- [ ] Paywall on email routes
- [ ] Upgrade CTAs in UI
- [ ] Test email features with subscription
- [ ] Billing integration

**Week 9-10: CMS Add-On**
- [ ] Feature flag for CMS
- [ ] Paywall on CMS routes
- [ ] Landing page templates
- [ ] Form builder for paid users
- [ ] Domain connection docs

### Phase 4: AI Phone System (Weeks 11-12)

**Week 11: Bland.ai Integration**
- [ ] Bland.ai API research
- [ ] Phone number provisioning
- [ ] Call routing setup
- [ ] Webhook handling
- [ ] Test calls end-to-end

**Week 12: AI Phone Feature**
- [ ] Feature flag for AI phone
- [ ] Paywall implementation
- [ ] Usage tracking (minutes)
- [ ] Overage handling
- [ ] Admin settings for AI behavior

### Phase 5: Polish & Launch (Weeks 13-16)

**Week 13: Landing Page & Marketing**
- [ ] New marketing site (not tenant-specific)
- [ ] Feature comparison page
- [ ] Pricing page
- [ ] Video demo
- [ ] Customer testimonials

**Week 14: Analytics & Monitoring**
- [ ] Usage tracking per tenant
- [ ] Conversion funnel analytics
- [ ] Performance monitoring
- [ ] Error tracking
- [ ] Billing dashboard for admin

**Week 15: Testing & Documentation**
- [ ] End-to-end test suite
- [ ] Load testing (simulate 1000 tenants)
- [ ] Security audit
- [ ] User documentation
- [ ] API documentation (if applicable)

**Week 16: Soft Launch**
- [ ] Invite beta users
- [ ] Gather feedback
- [ ] Fix critical bugs
- [ ] Prepare for public launch
- [ ] Set up customer support system

---

## Go-to-Market Strategy

### Launch Plan

**Phase 1: Friends & Family (Week 16)**
- Dad's inspector friends (10 users)
- Diego and restaurant contacts (5 users)
- Lawn care friend (1 user)
- Home building contact (1 user)
- Other personal network (10 users)
- **Goal:** 25 beta users, gather feedback

**Phase 2: Industry Communities (Weeks 17-20)**
- Home inspector forums/Facebook groups
- Contractor communities
- Service business subreddits
- LinkedIn posts in relevant groups
- **Goal:** 100 free tier users

**Phase 3: Content Marketing (Weeks 21-24)**
- Blog posts: "How I built a booking system for my dad"
- YouTube videos: Service business tools comparison
- LinkedIn posts: Building in public updates
- Product Hunt launch
- **Goal:** 500 free tier users

**Phase 4: Paid Acquisition (Weeks 25+)**
- Google Ads (service business keywords)
- Facebook/Instagram ads
- Retargeting campaigns
- Influencer partnerships
- **Goal:** 1,000+ free tier users, $10K MRR

### Pricing Psychology

**Anchoring Strategy:**
- Show Pro Bundle ($149) first
- Makes individual add-ons ($29-79) seem affordable
- Emphasizes value of bundle savings

**Social Proof:**
- "Join 1,000+ service professionals"
- Customer testimonials with photos
- Case studies with revenue impact

**Urgency/Scarcity:**
- "First 100 signups get lifetime 20% discount"
- "Limited spots for beta users"
- "Free tier may become limited trial in future"

**Value Communication:**
- Calculator: "Save 10 hours/week = $500/month value"
- ROI: "Pay $29/month, win one extra job = $500+"
- Comparison: "Housecall Pro: $169/month, CLIENTBRIDGE: $29-149/month"

---

## Financial Projections

### Revenue Model Assumptions

**Free Tier:**
- 70% of all users stay on free forever
- 20% upgrade to Email or CMS ($29-39/month)
- 8% upgrade to Team or AI Phone ($79-99/month)
- 2% upgrade to Pro Bundle ($149/month)

**LTV Calculations:**
```
Average paid user:
$29 (20% weight) + $39 (20%) + $79 (8%) + $99 (8%) + $149 (2%) = ~$45/month average

Assumed churn: 8% monthly
Average LTV: $45 / 0.08 = $562.50 per paid customer

Target CAC: $150 (3.75:1 LTV:CAC ratio)
```

### Growth Projections (Conservative)

**Q1 2026 (Months 1-3):**
- Free tier users: 100 → 500 → 1,000
- Paid users: 10 → 50 → 100
- MRR: $450 → $2,250 → $4,500
- **Q1 Total:** 1,000 free, 100 paid, $4.5K MRR

**Q2 2026 (Months 4-6):**
- Free tier users: 1,500 → 2,000 → 2,500
- Paid users: 150 → 200 → 250
- MRR: $6,750 → $9,000 → $11,250
- **Q2 Total:** 2,500 free, 250 paid, $11.25K MRR

**Q3 2026 (Months 7-9):**
- Free tier users: 3,000 → 4,000 → 5,000
- Paid users: 350 → 500 → 650
- MRR: $15,750 → $22,500 → $29,250
- **Q3 Total:** 5,000 free, 650 paid, $29.25K MRR

**Q4 2026 (Months 10-12):**
- Free tier users: 6,000 → 7,500 → 10,000
- Paid users: 800 → 1,000 → 1,250
- MRR: $36,000 → $45,000 → $56,250
- **Q4 Total:** 10,000 free, 1,250 paid, $56.25K MRR

**End of 2026 Summary:**
- Total users: 11,250 (10,000 free + 1,250 paid)
- MRR: $56,250
- ARR: $675,000
- Conversion rate: 11.1% (free to paid)

### Cost Structure

**Fixed Costs (Monthly):**
- Laravel Cloud hosting: $250 (estimated for scale)
- Database: $100
- Redis/caching: $50
- Domain/SSL: $10
- Email sending (Mailgun): $50
- SMS (Twilio): $100
- Total infrastructure: **$560/month**

**Variable Costs (per user):**
- Google Calendar API: Free (within limits)
- Stripe fees: 2.9% + $0.30 per transaction
- Bland.ai (AI phone): $0.09/min (user pays through plan)
- Estimated variable cost per paid user: **$5/month**

**Operating Costs:**
- Customer support (part-time): $2,000/month (starting Q2)
- Marketing/ads: $2,000/month (starting Q2)
- Software/tools: $200/month
- Total operating: **$4,200/month** (at scale)

**Break-Even Analysis:**
```
Fixed + Operating = $560 + $4,200 = $4,760/month
Average revenue per paid user = $45/month
Variable cost per paid user = $5/month
Net revenue per paid user = $40/month

Break-even: $4,760 / $40 = 119 paid users
Expected to hit: Month 4 (Q2 2026)
```

---

## Competitive Analysis

### Direct Competitors

#### Calendly
**Pricing:** $10-16/user/month  
**Strengths:**
- Simple, focused on booking only
- Great UX
- Wide integration ecosystem

**Weaknesses:**
- No CRM
- No email management
- No industry-specific features
- Doesn't understand service businesses

**CLIENTBRIDGE Advantage:**
- Free tier is competitive with Calendly paid
- Add CRM features Calendly doesn't have
- Service business specific

#### Housecall Pro
**Pricing:** $169-424/month  
**Strengths:**
- Full suite for service businesses
- Job management
- Invoicing/payments
- Customer management

**Weaknesses:**
- Expensive (prohibitive for small businesses)
- Complex onboarding
- Overkill for solo operators

**CLIENTBRIDGE Advantage:**
- 85% cheaper
- Simpler, faster to start
- Free tier for small users
- Modular (pay for what you need)

#### Square Appointments
**Pricing:** Free tier, then $29-69/month  
**Strengths:**
- Free tier exists
- Square payment integration
- Good for retail/appointments

**Weaknesses:**
- Limited customization
- Not built for service businesses
- Weak CRM features

**CLIENTBRIDGE Advantage:**
- Better for property-based services
- More powerful CRM
- Google Workspace native

### Indirect Competitors

#### Toast (Restaurants)
**Pricing:** Custom (expensive)  
**Market Cap:** $13B+  
**Our Opportunity:** Diego hates Toast. Service businesses (non-restaurant) underserved.

#### Jobber
**Pricing:** $49-249/month  
**Focus:** Field service businesses  
**Our Advantage:** Cheaper, simpler, free tier

---

## Risk Analysis & Mitigation

### Technical Risks

**Risk 1: Multi-tenant data leakage**
- **Impact:** Critical (legal liability)
- **Probability:** Low (with good testing)
- **Mitigation:** 
  - Comprehensive test suite
  - Security audit before launch
  - Bug bounty program

**Risk 2: Scaling challenges**
- **Impact:** High (poor UX, churn)
- **Probability:** Medium (at 10K+ users)
- **Mitigation:**
  - Redis caching strategy
  - Database indexing
  - CDN for assets
  - Load testing at each milestone

**Risk 3: Google API rate limits**
- **Impact:** Medium (booking failures)
- **Probability:** Medium (per-user limits)
- **Mitigation:**
  - Implement exponential backoff
  - Queue system for syncs
  - User education on limits

### Business Risks

**Risk 1: Free tier cannibalizes paid**
- **Impact:** High (no revenue)
- **Probability:** Medium (if limits too generous)
- **Mitigation:**
  - 50 bookings/month cap (not enough for growing businesses)
  - Regular review of conversion rates
  - A/B test different limit points

**Risk 2: Low conversion rate (free to paid)**
- **Impact:** High (unprofitable)
- **Probability:** Medium (if value prop weak)
- **Mitigation:**
  - In-app upgrade prompts at strategic moments
  - Email nurture sequences
  - Feature comparison visible in UI
  - Target 15% conversion minimum

**Risk 3: Competitors respond with free tiers**
- **Impact:** Medium (harder to differentiate)
- **Probability:** Low (large companies rarely do free well)
- **Mitigation:**
  - Community building
  - Service business specialization
  - Speed to market advantage

### Market Risks

**Risk 1: Service businesses reluctant to adopt SaaS**
- **Impact:** Medium (slower growth)
- **Probability:** Medium (older demographic)
- **Mitigation:**
  - Freemium removes barrier
  - Focus on younger business owners
  - Testimonials from peers

**Risk 2: Economic downturn**
- **Impact:** High (businesses cut tools)
- **Probability:** Medium (macro conditions)
- **Mitigation:**
  - Free tier retains users
  - Focus on ROI messaging
  - Lock in annual contracts with discount

---

## Success Metrics & KPIs

### North Star Metric
**Number of monthly active paid users**

Why: Direct correlation to revenue, indicates product value and retention.

### Key Performance Indicators

**Acquisition:**
- Free tier signups per week
- Signup conversion rate (visitor to signup)
- Cost per acquisition (CAC)
- Signup source breakdown

**Activation:**
- % of signups who complete onboarding
- % who connect Google Calendar
- % who receive first booking
- Time to first booking

**Engagement:**
- Monthly active users (MAU)
- Bookings per user per month
- Feature usage (email, CMS, AI phone)
- Session duration

**Monetization:**
- Free to paid conversion rate
- Average revenue per user (ARPU)
- Time to conversion (days from signup to paid)
- Upsell rate (Email → Pro Bundle)

**Retention:**
- Monthly churn rate (target: <5%)
- Cohort retention curves
- Customer lifetime (months)
- Net Revenue Retention (NRR)

**Revenue:**
- Monthly Recurring Revenue (MRR)
- MRR growth rate
- Customer Lifetime Value (LTV)
- LTV:CAC ratio (target: >3:1)

### Dashboard Tracking

**Weekly Review:**
- New signups
- Conversions to paid
- Churn events
- MRR change
- Top support issues

**Monthly Review:**
- All KPIs above
- Cohort analysis
- Feature usage trends
- Competitive landscape
- Unit economics

**Quarterly Review:**
- Strategic pivot assessment
- Roadmap prioritization
- Financial projections update
- Team needs
- Fundraising considerations

---

## Next Actions (Prioritized)

### Immediate (This Week)
1. [ ] Create detailed technical specification for multi-tenant architecture
2. [ ] Design database schema with tenant_id columns
3. [ ] Sketch out onboarding flow wireframes
4. [ ] Research Stripe subscription products setup
5. [ ] Draft pricing page copy

### Short Term (Next 2 Weeks)
1. [ ] Build multi-tenant database migrations
2. [ ] Implement BelongsToTenant trait
3. [ ] Create Google OAuth signup flow
4. [ ] Build onboarding wizard
5. [ ] Test tenant isolation thoroughly

### Medium Term (Weeks 3-6)
1. [ ] Adapt booking system for multi-tenant
2. [ ] Integrate Stripe subscriptions
3. [ ] Build upgrade/downgrade flows
4. [ ] Implement usage tracking (bookings/month)
5. [ ] Create admin dashboard for tenants

### Long Term (Weeks 7-16)
1. [ ] Add paid feature add-ons (email, CMS)
2. [ ] Integrate Bland.ai for AI phone system
3. [ ] Build marketing landing page
4. [ ] Launch beta with 25 users
5. [ ] Scale to 1,000 free tier users

---

## Open Questions

1. **Should we build mobile apps or focus on PWA first?**
   - Leaning: PWA first (faster, cheaper, still "app-like")

2. **How do we handle custom domains for free tier users?**
   - Leaning: Paid feature only (subdomain free)

3. **Should AI phone system be built in-house or always use Bland.ai?**
   - Leaning: Bland.ai initially, in-house if volume justifies

4. **What's our customer support strategy at scale?**
   - Leaning: Discord community for free, email for paid, priority for Pro+

5. **Should we raise funding or bootstrap?**
   - Leaning: Bootstrap to $10K MRR, then decide

6. **Do we need a co-founder?**
   - Leaning: Not yet, but consider for GTM/sales at $25K MRR

7. **How do we compete with Calendly's brand recognition?**
   - Leaning: Don't compete directly, focus on "Calendly + CRM for service businesses"

8. **What's our international strategy?**
   - Leaning: US only initially, expand after product-market fit

---

## Conclusion

This freemium multi-tenant strategy transforms CLIENTBRIDGE from a nice whitelabel tool into a scalable SaaS platform with clear path to $675K ARR by end of 2026.

**The Unlock:** Free tier removes friction, drives massive user adoption, creates network effects through booking pages.

**The Monetization:** Add-ons (email, CMS, AI phone) solve progressively more complex needs as businesses grow.

**The Validation:** Jason Gilmore (who runs 13 multi-million ARR SaaS companies) sees the potential. Dad's business uses it in production. Diego wants to use it for his restaurant.

**The Next Step:** Build the multi-tenant foundation over next 16 weeks, launch beta in Q1 2026, scale to 1,000 users by end of Q1.

**The Vision:** CLIENTBRIDGE becomes the default booking + CRM + communication tool for service professionals who don't need (or can't afford) complex enterprise solutions like Housecall Pro, but need more than just Calendly.

---

**Last Updated:** November 24, 2025  
**Next Review:** December 1, 2025 (after initial technical planning)  
**Owner:** Alexander Ramsey  
**Advisor:** Jason Gilmore (Xenon Partners)
