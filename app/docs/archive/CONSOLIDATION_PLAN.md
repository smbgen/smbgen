# Feature Consolidation & Enhancement Plan

## Overview
Consolidate 3 feature flags (cms, home_landing, lead_form) into one unified CMS feature with enhanced form building and lead management.

## Phase 1: Feature Flag Consolidation ✅

### Current State
- `FEATURE_CMS` - CMS page management
- `FEATURE_HOME_LANDING` - Homepage landing page
- `FEATURE_LEADFORM` - Lead form capture (if exists)

### Target State
- Single `FEATURE_CMS` flag that controls:
  - CMS page management
  - Form builder for pages
  - Lead capture through forms
  - Landing pages (including home)

### Tasks
1. ✅ Keep `FEATURE_CMS` as the main flag
2. Remove `FEATURE_HOME_LANDING` and `FEATURE_LEADFORM` from config
3. Update .env.example to remove old flags
4. Update all blade views using old flags to use `business.features.cms`
5. Update routes using old flags

---

## Phase 2: Admin Dashboard Reorganization

### Current Dashboard Structure
- Email Services section
- Lead Form Generation card (to be removed)
- Various other service cards

### New Dashboard Structure

#### CMS Services Section (New/Enhanced)
- **CMS Page Management** card
  - Link to manage CMS pages
  - Link to create new page
- **Lead Form Submissions** card  
  - Link to view all lead submissions
  - Quick stats (total leads, today's leads)
  - Link to export leads
- **Form Builder** integrated into CMS page create/edit

#### Quick Links Section (New)
- **Manage Clients** - Link to clients index
- **Recent Bookings** - Link to bookings with recent filter
- **Lead Submissions** - Link to lead_forms index
- **Convert Leads** - Quick access to lead conversion

### Cards to Remove
- "Lead Form Generation" standalone card (functionality moves to CMS)

### Cards to Keep
- Email Services
- Quick Client Creation
- All other existing services

---

## Phase 3: Database Schema Updates

### Clients Table
- ✅ Check if `property_address` exists
- Add if missing: `property_address` (TEXT, nullable)

### Bookings Table  
- Add `break_period_minutes` (INTEGER, nullable) - for individual booking overrides
- Add `property_address` (TEXT, nullable) - auto-populate from client if exists

### Booking Settings/Config
- Add global `default_break_period_minutes` setting
- Example: 45min appointments on the hour = 45min slot + 15min break

### Lead Forms
- ✅ Already has `cms_page_id` and `form_data`

---

## Phase 4: CMS Admin UI Enhancements

### CMS Index Page (`admin/cms/index.blade.php`)
**Add:**
- Stats card showing: Total pages, Published pages, Pages with forms, Total form submissions
- Tabs or filters: All Pages | Landing Pages | Form Pages
- Column showing form submission count per page
- Quick action: "View Submissions" button for pages with forms

### CMS Create/Edit Pages (`admin/cms/create.blade.php`, `admin/cms/edit.blade.php`)
**Add Form Builder Section:**
```
┌─────────────────────────────────────┐
│ Form Builder (Optional)             │
├─────────────────────────────────────┤
│ ☐ Enable Form on this Page         │
│                                     │
│ When enabled:                       │
│ ┌─────────────────────────────────┐ │
│ │ Form Fields                     │ │
│ │ [+ Add Field Button]            │ │
│ │                                 │ │
│ │ Field 1: [Name ▼] [Text ▼]     │ │
│ │   Label: [Your Name]            │ │
│ │   Placeholder: [John Doe]       │ │
│ │   Required: ☑                   │ │
│ │   [Remove]                      │ │
│ │                                 │ │
│ │ Field 2: [Email ▼] [Email ▼]   │ │
│ │   Label: [Email Address]        │ │
│ │   Required: ☑                   │ │
│ │   [Remove]                      │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Submit Button Text:                 │
│ [Send Message      ]                │
│                                     │
│ Success Message:                    │
│ [Thank you! We'll be in touch.]     │
│                                     │
│ Redirect After Submit (optional):   │
│ [/thank-you        ]                │
│                                     │
│ ☐ Create Lead from submissions     │
│                                     │
│ Field Mapping:                      │
│ • name field → Lead name            │
│ • email field → Lead email          │
│ • message field → Lead message      │
│ • Other fields → form_data JSON     │
└─────────────────────────────────────┘
```

**Field Type Options:**
- text
- email
- tel
- textarea
- select (with options)
- checkbox
- radio
- number
- date

---

## Phase 5: Lead Management Views

### Create Lead Index View (`resources/views/admin/leads/index.blade.php`)
**Features:**
- Table showing all lead_forms
- Columns: Name, Email, Source (CMS page or other), Date, Status
- Action: "Convert to Client" button (existing functionality)
- Filter by source: All | CMS Forms | API | Other
- Search by name/email
- Date range filter
- Export to CSV
- Bulk actions: Convert selected, Delete selected

### Lead Detail View (`resources/views/admin/leads/show.blade.php`)
**Show:**
- Standard fields: name, email, message
- Custom form_data (if exists) displayed in a nice card
- Source information (which CMS page)
- IP address, user agent, referer
- Timestamps
- Action buttons: Convert to Client, Delete
- If from CMS page: Link to view the page

---

## Phase 6: Booking System Enhancements

### Add Break Period Feature

**Database Migration:**
```php
Schema::table('bookings', function (Blueprint $table) {
    $table->integer('break_period_minutes')->nullable()->after('duration');
    $table->text('property_address')->nullable()->after('notes');
});
```

**Business Settings:**
Add to config or settings table:
- `default_appointment_duration` (e.g., 45 minutes)
- `default_break_period` (e.g., 15 minutes)
- This means: 45min slots at :00, :00, :00 with 15min buffer

**Booking Form Updates:**
- Show property_address field (auto-fill from client if available)
- Add optional break period override
- Show calculated end time including break
- Example: "45 minute appointment + 15 minute break = Available again at 1:00 PM"

### Add Convert to Client for Bookings

**Booking Show/Index Views:**
Add button: "Convert to Client"
- If customer_email doesn't exist in clients table:
  - Show confirmation: "Create client from booking?"
  - Pre-fill form with booking data:
    - name = customer_name
    - email = customer_email
    - phone = customer_phone
    - notes = booking notes
    - property_address = booking property_address (if exists)
- If customer already exists as client:
  - Show message: "Already a client" with link to client profile

---

## Phase 7: Public Form Submission

### Create Form Submission Controller
`app/Http/Controllers/CmsFormSubmissionController.php`

**Endpoint:** `POST /cms/form/{slug}`

**Logic:**
1. Find CMS page by slug
2. Validate form has has_form enabled
3. Validate submitted data against form_fields definition
4. Create LeadForm record:
   - Map standard fields (name, email, message)
   - Store custom fields in form_data JSON
   - Set cms_page_id
   - Capture ip_address, user_agent, referer
   - Set source_site from page slug/URL
5. Optionally send notification email
6. Return success message or redirect

---

## Phase 8: Client Model Enhancement

### Add Property Address

**Check if exists:**
```sql
SELECT * FROM pragma_table_info('clients') WHERE name='property_address';
```

**If not exists, create migration:**
```php
Schema::table('clients', function (Blueprint $table) {
    $table->text('property_address')->nullable()->after('phone');
});
```

**Update Client Model:**
Add `property_address` to fillable array

**Update Client Forms:**
- Add property_address field to create/edit forms
- Make it textarea for multi-line addresses
- Optional field

---

## Phase 9: Auto-populate Property Address

### Booking Form
When selecting a client (if you have client selection):
```javascript
// On client select
fetch(`/api/clients/${clientId}`)
    .then(r => r.json())
    .then(client => {
        if (client.property_address) {
            document.getElementById('property_address').value = client.property_address;
        }
    });
```

### Lead to Client Conversion
When converting lead to client:
- If form_data contains property_address, pre-fill it
- Allow admin to edit before saving

---

## Implementation Order

### Sprint 1: Core Consolidation
1. ✅ Feature flag consolidation
2. ✅ Database migrations (leads + cms_page_id)
3. Lead index view
4. Client property_address migration

### Sprint 2: Booking Enhancements  
1. Booking break period migration
2. Booking property_address migration  
3. Update booking form with new fields
4. Update booking settings
5. Add convert to client button for bookings

### Sprint 3: Form Builder UI
1. Enhance CMS create/edit with form builder
2. Add field management (add/remove/reorder)
3. Field type selection
4. Form preview

### Sprint 4: Form Submission & Lead Management
1. Public form submission controller
2. Form validation
3. Lead creation from forms
4. Lead index view with filters
5. Lead detail view showing form_data

### Sprint 5: Dashboard Reorganization
1. Add CMS services section to dashboard
2. Add quick links section
3. Remove lead form generation card
4. Update all feature flag references

### Sprint 6: Polish & Testing
1. Update all routes with old flags
2. Update all blade views with old flags  
3. Write/update tests
4. Update documentation
5. Run full test suite

---

## Testing Checklist

- [ ] CMS pages CRUD still works
- [ ] Form builder in CMS pages works
- [ ] Public form submission creates leads
- [ ] Lead index shows all leads with filters
- [ ] Convert lead to client works
- [ ] Convert booking to client works
- [ ] Client property_address saves/displays
- [ ] Booking property_address saves/displays
- [ ] Booking break period calculation works
- [ ] Auto-populate property_address from client works
- [ ] Dashboard shows correct sections
- [ ] All old feature flags removed
- [ ] No broken routes or views

---

## Files to Modify

### Config
- ✅ `config/business.php` - Remove old flags, keep cms
- `.env.example` - Remove old flags

### Migrations
- ✅ `add_cms_page_id_to_lead_forms_table.php`
- `add_property_address_to_clients_table.php`
- `add_booking_enhancements.php` (break_period, property_address)

### Models
- ✅ `app/Models/LeadForm.php`
- ✅ `app/Models/CmsPage.php`
- `app/Models/Client.php` (add property_address)
- `app/Models/Booking.php` (add break_period, property_address)

### Controllers
- `app/Http/Controllers/CmsFormSubmissionController.php` (new)
- `app/Http/Controllers/Admin/LeadController.php` (new or enhance)
- `app/Http/Controllers/Admin/BookingController.php` (add convert to client)
- `app/Http/Controllers/Admin/CmsPageController.php` (enhance for form builder)

### Views - Admin Dashboard
- `resources/views/admin/dashboard.blade.php` - Major reorganization

### Views - CMS
- `resources/views/admin/cms/index.blade.php` - Add stats, filters
- `resources/views/admin/cms/create.blade.php` - Add form builder UI
- `resources/views/admin/cms/edit.blade.php` - Add form builder UI

### Views - Leads (New)
- `resources/views/admin/leads/index.blade.php` (new)
- `resources/views/admin/leads/show.blade.php` (new)

### Views - Clients
- `resources/views/admin/clients/create.blade.php` - Add property_address
- `resources/views/admin/clients/edit.blade.php` - Add property_address
- `resources/views/admin/clients/show.blade.php` - Show property_address

### Views - Bookings
- `resources/views/admin/bookings/create.blade.php` - Add break_period, property_address
- `resources/views/admin/bookings/edit.blade.php` - Add break_period, property_address
- `resources/views/admin/bookings/show.blade.php` - Add convert to client button
- `resources/views/admin/bookings/index.blade.php` - Add convert to client button

### Routes
- `routes/web.php` - Update feature flag checks, add new lead routes

### Tests
- Tests for all new functionality
- Update existing tests for changed behavior
