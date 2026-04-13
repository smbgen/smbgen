````markdown
# SMBGEN Development Roadmap

**⚠️ DEPRECATED: This document has been consolidated into [TACTICAL_ROADMAP.md](./TACTICAL_ROADMAP.md)**

*The content below is kept for historical reference but may be outdated.*

---

## 🎯 **MILESTONE 1: Complete Tier 1 Service Offering** 
**Priority: CRITICAL** | **Timeline: 1-2 weeks** | **Revenue Impact: $499 per client**

### **Current Status: 70% Complete**
- ✅ Cyber Audit Assistant (LLM chatbot) - **IMPLEMENTED**
- ❌ Calendly Integration - **NEEDS IMPLEMENTATION**
- ❌ Stripe Checkout - **NEEDS IMPLEMENTATION**

### **User Stories:**

#### **US-1.1: Calendly Integration**
**As a** client  
**I want to** schedule a cybersecurity audit  
**So that** I can book a consultation time that works for me

**Acceptance Criteria:**
- [ ] Calendly widget embedded in Cyber Audit Assistant page
- [ ] Calendar shows available 1-hour audit slots
- [ ] Booking creates appointment record in database
- [ ] Admin receives notification of new booking
- [ ] Client receives confirmation email

**Technical Implementation:**
```php
// New migration: appointments table enhancement
$table->string('calendly_event_id')->nullable();
$table->string('stripe_payment_intent_id')->nullable();
$table->enum('status', ['scheduled', 'paid', 'completed', 'cancelled']);

// New controller: CalendlyWebhookController
// New service: CalendlyService
```

#### **US-1.2: Stripe Checkout Integration**
**As a** client  
**I want to** pay $499 for my cybersecurity audit  
**So that** I can complete the booking process

**Acceptance Criteria:**
- [ ] Stripe checkout button after Calendly booking
- [ ] $499 flat fee with tax calculation
- [ ] Payment confirmation and receipt
- [ ] Appointment status updates to 'paid'
- [ ] Admin dashboard shows payment status

**Technical Implementation:**
```php
// New migration: payments table
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('appointment_id')->constrained();
    $table->string('stripe_payment_intent_id');
    $table->decimal('amount', 10, 2);
    $table->string('currency')->default('usd');
    $table->enum('status', ['pending', 'succeeded', 'failed']);
    $table->timestamps();
});

// New controller: StripeWebhookController
// New service: StripeService
```

#### **US-1.3: Enhanced Cyber Audit Assistant**
**As a** client  
**I want to** receive a comprehensive audit report  
**So that** I understand my security posture

**Acceptance Criteria:**
- [ ] Generate PDF audit report after consultation
- [ ] Email report to client
- [ ] Store report in client's file section
- [ ] Admin can view all audit reports

---

## 🎯 **MILESTONE 2: Client Portal Enhancement**
**Priority: HIGH** | **Timeline: 2-3 weeks**

### **Current Status: 60% Complete**
- ✅ Basic dashboard structure
- ✅ File section (basic)
- ✅ Messaging system (basic)
- ❌ File upload/download functionality
- ❌ Enhanced messaging features

### **User Stories:**

#### **US-2.1: File Management System**
**As a** client  
**I want to** upload and download files  
**So that** I can share documents with my cybersecurity team

**Acceptance Criteria:**
- [ ] File upload with drag & drop
- [ ] File type validation (PDF, DOC, images)
- [ ] File size limits (10MB max)
- [ ] Download functionality
- [ ] File organization by folders
- [ ] Search and filter files

#### **US-2.2: Enhanced Messaging System**
**As a** client  
**I want to** have secure conversations with my team  
**So that** I can discuss sensitive security matters

**Acceptance Criteria:**
- [ ] Real-time messaging with WebSockets
- [ ] File attachments in messages
- [ ] Message read receipts
- [ ] Message threading
- [ ] Admin can assign conversations to team members

#### **US-2.3: Dashboard Analytics**
**As a** client  
**I want to** see my security status at a glance  
**So that** I can track my progress

**Acceptance Criteria:**
- [ ] Security score visualization
- [ ] Recent activity feed
- [ ] Upcoming appointments
- [ ] Pending tasks/checklist items
- [ ] File storage usage

---

## 🎯 **MILESTONE 3: Admin Panel Enhancement**
**Priority: HIGH** | **Timeline: 2-3 weeks**

### **Current Status: 40% Complete**
- ✅ Basic admin dashboard
- ✅ Client management interface
- ❌ Advanced client management features
- ❌ Team member management

### **User Stories:**

#### **US-3.1: Advanced Client Management**
**As an** admin  
**I want to** manage all client interactions  
**So that** I can provide excellent service

**Acceptance Criteria:**
- [ ] Client activity timeline
- [ ] Payment history and invoices
- [ ] Audit report management
- [ ] Client communication history
- [ ] Client status tracking (active, inactive, pending)

#### **US-3.2: Team Member Management**
**As an** admin  
**I want to** manage team members and their access  
**So that** I can control who can access client data

**Acceptance Criteria:**
- [ ] Add/remove team members
- [ ] Role-based permissions (admin, analyst, support)
- [ ] Activity logs for team members
- [ ] Client assignment to team members

#### **US-3.3: Shared Documents Repository**
**As an** admin  
**I want to** manage shared documents for each client  
**So that** I can provide consistent resources

**Acceptance Criteria:**
- [ ] Upload templates and resources
- [ ] Organize by client and category
- [ ] Version control for documents
- [ ] Client access tracking

---

## 🎯 **MILESTONE 4: Security & Authentication Enhancement**
**Priority: MEDIUM** | **Timeline: 1-2 weeks**

### **Current Status: 30% Complete**
- ✅ IP-based access restriction
- ✅ Basic role-based access
- ❌ Passkey authentication
- ❌ Google OAuth integration
- ❌ Audit logging

### **User Stories:**

#### **US-4.1: Passkey Authentication**
**As a** user  
**I want to** use passkeys for secure login  
**So that** I don't need to remember passwords

**Acceptance Criteria:**
- [ ] Passkey registration during onboarding
- [ ] Passkey login option
- [ ] Fallback to password authentication
- [ ] Multi-device passkey support

#### **US-4.2: Google OAuth Integration**
**As a** user  
**I want to** login with my Google account  
**So that** I can access the platform easily

**Acceptance Criteria:**
- [ ] Google OAuth login button
- [ ] Account linking with existing accounts
- [ ] Profile sync from Google
- [ ] Secure token handling

#### **US-4.3: Comprehensive Audit Logging**
**As an** admin  
**I want to** track all system activities  
**So that** I can monitor security and compliance

**Acceptance Criteria:**
- [ ] Login/logout events
- [ ] File access events
- [ ] Data modification events
- [ ] Admin action logging
- [ ] Exportable audit reports

---

## 🎯 **MILESTONE 5: Marketing & Lead Capture**
**Priority: MEDIUM** | **Timeline: 1-2 weeks**

### **Current Status: 20% Complete**
- ✅ Basic lead form handling
- ❌ L7 x Old Line Cyber landing page integration
- ❌ Production deployment

### **User Stories:**

#### **US-5.1: Landing Page Integration**
**As a** marketing team  
**I want to** integrate the L7 landing page  
**So that** we can capture leads effectively

**Acceptance Criteria:**
- [ ] Embed L7 landing page in SMBGEN
- [ ] Lead form submission handling
- [ ] Lead scoring and qualification
- [ ] CRM integration for lead management

#### **US-5.2: Production Deployment**
**As a** development team  
**I want to** deploy to production  
**So that** clients can access the platform

**Acceptance Criteria:**
- [ ] Deploy to houston1.oldlinecyber.com
- [ ] SSL certificate configuration
- [ ] Database migration and seeding
- [ ] Environment configuration
- [ ] Monitoring and logging setup

---

## 🎯 **MILESTONE 6: Advanced Features & Optimization**
**Priority: LOW** | **Timeline: 2-4 weeks**

### **User Stories:**

#### **US-6.1: Advanced Analytics Dashboard**
**As an** admin  
**I want to** see business metrics  
**So that** I can make data-driven decisions

**Acceptance Criteria:**
- [ ] Revenue tracking
- [ ] Client acquisition metrics
- [ ] Service utilization analytics
- [ ] Performance metrics

#### **US-6.2: API Development**
**As a** development team  
**I want to** provide API access  
**So that** we can integrate with other systems

**Acceptance Criteria:**
- [ ] RESTful API endpoints
- [ ] API authentication
- [ ] Rate limiting
- [ ] API documentation

---

## 📊 **Implementation Priority Matrix**

| Feature | Revenue Impact | Development Effort | User Value | Priority Score |
|---------|---------------|-------------------|------------|----------------|
| Stripe Integration | 🔴 HIGH | 🟡 MEDIUM | 🟢 HIGH | **9/10** |
| Calendly Integration | 🟢 HIGH | 🟡 MEDIUM | 🟢 HIGH | **9/10** |
| File Upload System | 🟡 MEDIUM | 🟢 LOW | 🟢 HIGH | **7/10** |
| Enhanced Messaging | 🟡 MEDIUM | 🟡 MEDIUM | 🟢 HIGH | **7/10** |
| Passkey Auth | 🟡 MEDIUM | 🟢 LOW | 🟡 MEDIUM | **6/10** |
| Production Deployment | 🟢 HIGH | 🟡 MEDIUM | 🟢 HIGH | **8/10** |

---

## 🚀 **Next Steps (Immediate Action Items)**

### **Week 1: Complete Tier 1 Service**
1. **Day 1-2:** Implement Calendly integration
2. **Day 3-4:** Implement Stripe checkout
3. **Day 5:** Test end-to-end booking flow
4. **Weekend:** Polish UI/UX

### **Week 2: Client Portal Enhancement**
1. **Day 1-3:** File upload/download system
2. **Day 4-5:** Enhanced messaging features

### **Week 3: Admin Panel & Security**
1. **Day 1-3:** Advanced admin features
2. **Day 4-5:** Security enhancements

---

## 💰 **Revenue Projections**

**Tier 1 Service ($499/audit):**
- **Month 1:** 5 audits = $2,495
- **Month 2:** 10 audits = $4,990
- **Month 3:** 15 audits = $7,485

**Total 3-month projection: $14,970**

---

## 🔧 **Technical Stack Summary**

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Blade templates, Bootstrap 5, JavaScript
- **Database:** MySQL with optimized migrations
- **Payment:** Stripe integration
- **Scheduling:** Calendly integration
- **Authentication:** Passkey + Google OAuth
- **File Storage:** Local storage (future: S3)
- **Hosting:** houston1.oldlinecyber.com

---

*Last Updated: August 10, 2025*
*Status: Ready for Implementation*
