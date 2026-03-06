# CMS Forms Integration with Lead Forms

## Overview

CMS pages with forms now integrate directly with the existing **Lead Forms** system. This means:

✅ CMS form submissions automatically create Lead records  
✅ Leads appear in the existing leads table/admin view  
✅ Admins can use the existing "Convert to Client" button  
✅ No duplicate workflows or conversion logic needed  

## How It Works

### 1. Admin Creates CMS Page with Form

Admin creates a CMS page and enables `has_form`:
- Defines custom form fields in JSON format
- Maps standard fields (name, email, message) 
- Can add any custom fields (phone, company, interest, etc.)

### 2. Visitor Submits Form

When a visitor submits a CMS form:
1. Form data is validated
2. A `LeadForm` record is created with:
   - `cms_page_id` - Links back to the CMS page
   - `name`, `email`, `message` - Standard lead fields
   - `source_site` - Automatically set from page
   - `form_data` - JSON with all custom field data
   - `ip_address`, `user_agent`, `referer` - Tracking data

### 3. Admin Views Lead in Leads Table

The lead appears in the existing leads table with:
- Source showing which CMS page it came from
- All custom form data accessible in `form_data` JSON
- Standard lead information (name, email, message)

### 4. Admin Converts to Client

Admin uses the existing "Convert to Client" button:
- Existing conversion logic handles the transformation
- No changes needed to conversion workflow

## Database Schema

### lead_forms Table (Updated)

```sql
id                   INTEGER PRIMARY KEY
cms_page_id          INTEGER NULL             -- NEW: Links to CMS page
name                 VARCHAR(255)
email                VARCHAR(255)
message              TEXT
source_site          VARCHAR(255)
notification_email   VARCHAR(255)
ip_address           VARCHAR(45)
user_agent           TEXT
referer              VARCHAR(255)
form_data            JSON NULL                -- NEW: All custom form data
created_at           TIMESTAMP
updated_at           TIMESTAMP

FOREIGN KEY (cms_page_id) REFERENCES cms_pages(id) ON DELETE SET NULL
```

**Why `cms_page_id` is nullable:**
- Leads can come from other sources (API, manual entry, other forms)
- Only CMS form submissions will have a `cms_page_id`

**Why `form_data` is separate from standard fields:**
- `name`, `email`, `message` are core lead fields used throughout the app
- `form_data` stores additional custom fields specific to each CMS form
- Makes it easy to display standard info while preserving custom data

### cms_pages Table (Form Fields)

```sql
has_form                  BOOLEAN DEFAULT FALSE
form_fields              JSON NULL
form_submit_button_text  VARCHAR(255) NULL
form_success_message     TEXT NULL
form_redirect_url        VARCHAR(255) NULL
```

## Field Mapping Strategy

### Standard Field Mapping

These form field names automatically map to LeadForm columns:
- `name` → `lead_forms.name`
- `email` → `lead_forms.email`
- `message` → `lead_forms.message`

### Custom Field Storage

All other fields are stored in the `form_data` JSON column:
```json
{
  "phone": "555-1234",
  "company": "Acme Corp",
  "interest": "Enterprise Plan",
  "budget": "10000",
  "timeline": "Q1 2025"
}
```

## Example Form Field Definition

```json
{
  "form_fields": [
    {
      "type": "text",
      "name": "name",
      "label": "Your Name",
      "required": true,
      "placeholder": "John Doe"
    },
    {
      "type": "email", 
      "name": "email",
      "label": "Email Address",
      "required": true,
      "placeholder": "john@example.com"
    },
    {
      "type": "tel",
      "name": "phone",
      "label": "Phone Number",
      "required": false,
      "placeholder": "555-1234"
    },
    {
      "type": "select",
      "name": "interest",
      "label": "I'm interested in",
      "required": true,
      "options": ["Starter Plan", "Pro Plan", "Enterprise Plan"]
    },
    {
      "type": "textarea",
      "name": "message",
      "label": "Tell us more",
      "required": false,
      "rows": 4
    }
  ]
}
```

## Model Relationships

### CmsPage Model
```php
public function leads(): HasMany
{
    return $this->hasMany(LeadForm::class);
}
```

Usage:
```php
$page = CmsPage::find(1);
$leads = $page->leads; // All leads from this page
$leadCount = $page->leads()->count();
```

### LeadForm Model
```php
public function cmsPage(): BelongsTo
{
    return $this->belongsTo(CmsPage::class);
}
```

Usage:
```php
$lead = LeadForm::find(1);
$page = $lead->cmsPage; // CMS page that generated this lead (if any)
```

## Implementation Checklist

### Backend (To Do)
- [ ] Create form submission controller endpoint
- [ ] Validate form data against field definitions
- [ ] Create LeadForm record from submission
- [ ] Map standard fields (name, email, message)
- [ ] Store custom fields in form_data JSON
- [ ] Set source_site from CMS page slug/URL
- [ ] Handle form success message/redirect
- [ ] Send notification email if configured

### Admin UI (To Do)
- [ ] Form builder in CMS page create/edit
- [ ] Add/remove form fields dynamically
- [ ] Configure field types, labels, validation
- [ ] Preview form before publishing
- [ ] View leads with CMS page source in leads table
- [ ] Display custom form_data in lead detail view

### Frontend (To Do)
- [ ] Render forms on public CMS pages
- [ ] Client-side validation
- [ ] AJAX form submission
- [ ] Display success message or redirect
- [ ] Loading states

## Benefits of This Approach

✅ **Reuses Existing System** - No need to build new lead/client conversion  
✅ **Single Source of Truth** - All leads in one table  
✅ **Flexible** - Can add any custom fields without schema changes  
✅ **Traceable** - Know which CMS page generated each lead  
✅ **Familiar Workflow** - Admins use existing leads UI  
✅ **Backward Compatible** - Non-CMS leads still work normally  

## Migration Notes

- Keep `cms_form_submissions` table for audit/backup purposes if desired
- `cms_page_id` nullable allows leads from other sources
- Existing `lead_forms` records unaffected (cms_page_id will be null)
- Feature flags still control CMS/landing page visibility
