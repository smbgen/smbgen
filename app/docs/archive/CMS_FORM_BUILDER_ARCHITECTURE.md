# CMS with Integrated Form Builder - Architecture Summary

## Overview

The CMS feature has been enhanced to include a powerful form builder, consolidating landing page and form functionality into a single, unified system. This simplifies the application architecture while providing maximum flexibility for creating custom pages with forms.

## Key Concept: CMS = Landing Pages + Forms

Instead of having separate systems for:
- Static landing pages
- Form pages  
- Content pages

We now have **one CMS system** that can handle all of these use cases through configuration.

## Database Schema

### CMS Pages Table (`cms_pages`)

**Core Content Fields:**
- `slug` - URL-friendly identifier (e.g., "home", "contact", "pricing")
- `title` - Page title
- `head_content` - Custom CSS/JS/meta tags
- `body_content` - Main HTML content
- `background_color` - Tailwind class for background
- `text_color` - Tailwind class for text
- `is_published` - Visibility control

**Call-to-Action Fields:**
- `cta_text` - Button text
- `cta_url` - Button link

**Form Builder Fields (NEW):**
- `has_form` - Boolean to enable form on page
- `form_fields` - JSON array of form field definitions
- `form_submit_button_text` - Customize submit button
- `form_success_message` - Message shown after submission
- `form_redirect_url` - Optional redirect after submission

### Form Submissions Table (`cms_form_submissions`)

- `cms_page_id` - Links to the CMS page
- `data` - JSON containing all form field values
- `ip_address` - Submitter's IP
- `user_agent` - Submitter's browser
- `created_at` - Submission timestamp

## Form Field Definition Structure

Form fields are stored as JSON in the `form_fields` column. Each field has:

```json
[
  {
    "name": "email",
    "label": "Email Address",
    "type": "email",
    "required": true,
    "placeholder": "you@example.com",
    "validation": "required|email"
  },
  {
    "name": "message",
    "label": "Your Message",
    "type": "textarea",
    "required": true,
    "placeholder": "Tell us what you need...",
    "validation": "required|min:10"
  },
  {
    "name": "phone",
    "label": "Phone Number",
    "type": "tel",
    "required": false,
    "placeholder": "(555) 123-4567",
    "validation": "nullable|phone"
  }
]
```

### Supported Field Types

- `text` - Single line text input
- `email` - Email address
- `tel` - Phone number
- `url` - Website URL
- `number` - Numeric input
- `textarea` - Multi-line text
- `select` - Dropdown (add `options` array)
- `radio` - Radio buttons (add `options` array)
- `checkbox` - Single checkbox
- `checkboxes` - Multiple checkboxes (add `options` array)
- `date` - Date picker
- `file` - File upload
- `hidden` - Hidden field

## Use Cases

### 1. Simple Landing Page (No Form)
```
has_form = false
body_content = HTML with headings, paragraphs, images
cta_text = "Get Started"
cta_url = "/register"
```

### 2. Contact Form Page
```
has_form = true
form_fields = [
  {"name": "name", "type": "text", "required": true},
  {"name": "email", "type": "email", "required": true},
  {"name": "message", "type": "textarea", "required": true}
]
form_submit_button_text = "Send Message"
form_success_message = "Thanks! We'll get back to you soon."
```

### 3. Lead Capture Landing Page
```
has_form = true
body_content = Marketing copy above the form
form_fields = [
  {"name": "email", "type": "email", "required": true},
  {"name": "company", "type": "text", "required": false}
]
form_redirect_url = "/thank-you"
```

### 4. Survey/Questionnaire Page
```
has_form = true
form_fields = [
  {"name": "satisfaction", "type": "radio", "options": ["Very Satisfied", "Satisfied", "Neutral", "Dissatisfied"]},
  {"name": "improvements", "type": "checkboxes", "options": ["Speed", "Features", "Design", "Support"]},
  {"name": "comments", "type": "textarea"}
]
```

## Feature Flag Integration

The system respects two feature flags:

1. **`FEATURE_CMS`** - Enables the entire CMS system
   - Admin CMS management at `/admin/cms`
   - Public page display at `/{slug}`
   
2. **`FEATURE_HOME_LANDING`** - Uses CMS for home page
   - When enabled, `/` looks for CMS page with slug="home"
   - Falls back to default landing page if no "home" page exists
   - When disabled, `/` shows login redirect (default behavior)

## Admin Workflow

### Creating a Landing Page with Form

1. Go to `/admin/cms` → Create New Page
2. Set slug (e.g., "contact")
3. Add page title and body content (marketing copy)
4. Check "Enable Form" checkbox
5. Click "Add Form Field" to add fields:
   - Choose field type
   - Set label and placeholder
   - Mark as required if needed
   - Add validation rules
6. Customize submit button text
7. Set success message or redirect URL
8. Publish the page

### Viewing Form Submissions

1. Go to `/admin/cms`
2. Click "View Submissions" next to any page with a form
3. See all submissions with timestamps
4. Export to CSV (future enhancement)

## Technical Implementation Notes

### Models

**CmsPage**
- Has relationship: `formSubmissions()`
- Casts `form_fields` to array automatically
- Casts `has_form` to boolean

**CmsFormSubmission**
- Belongs to `CmsPage`
- Casts `data` to array for easy access
- Stores IP and user agent for security/analytics

### Controllers

**Admin/CmsPageController**
- `index()` - List all pages with submission counts
- `create()` - Form to create new page with form builder
- `store()` - Validate and save page + form definition
- `edit()` - Edit existing page and form fields
- `update()` - Update page and form definition
- `destroy()` - Delete page and all submissions
- `submissions()` - View form submissions for a page (NEW)

**CmsPagePublicController**
- `show()` - Display public page with form if enabled
- `submitForm()` - Handle form submission (NEW)

### Routes

```php
// Public
GET /{slug} - Display CMS page (with form if has_form=true)
POST /{slug}/submit - Submit form data

// Admin
GET /admin/cms - List all pages
GET /admin/cms/create - Create new page
POST /admin/cms - Store new page
GET /admin/cms/{id}/edit - Edit page
PATCH /admin/cms/{id} - Update page
DELETE /admin/cms/{id} - Delete page
GET /admin/cms/{id}/submissions - View submissions (NEW)
```

## Benefits of Consolidation

### Before (Separate Systems)
- ❌ home_landing feature for static pages
- ❌ Separate lead form system
- ❌ Separate contact form handling
- ❌ Multiple databases/models to manage
- ❌ Duplicate form validation logic

### After (Unified CMS)
- ✅ One system handles all page types
- ✅ Reusable form builder for any page
- ✅ Consistent submission handling
- ✅ Single admin interface
- ✅ Centralized analytics

## Security Considerations

1. **Validation**: All form fields have validation rules
2. **CSRF Protection**: Laravel's built-in CSRF on all POST requests
3. **Rate Limiting**: Consider adding to prevent spam
4. **IP Tracking**: Store IP addresses for abuse detection
5. **Sanitization**: HTML is escaped by default in Blade

## Future Enhancements

- [ ] Drag-and-drop form field ordering
- [ ] Conditional field visibility
- [ ] Email notifications on form submission
- [ ] Webhook integrations (Zapier, etc.)
- [ ] Form submission exports (CSV, Excel)
- [ ] Spam protection (reCAPTCHA, honeypot)
- [ ] File upload handling and storage
- [ ] Multi-step forms
- [ ] Form analytics (conversion rates, drop-off points)
- [ ] A/B testing for forms

## Migration Path

For existing applications using separate landing page systems:

1. Enable `FEATURE_CMS=true`
2. Create CMS pages for existing landing pages
3. Migrate form definitions to `form_fields` JSON
4. Update routes to use `/{slug}` instead of custom routes
5. Remove old landing page controllers/views
6. Remove `FEATURE_HOME_LANDING` references in favor of CMS-based approach

## Example: Creating a "Contact Us" Page

```php
CmsPage::create([
    'slug' => 'contact',
    'title' => 'Contact Us',
    'body_content' => '<h1>Get In Touch</h1><p>We\'d love to hear from you!</p>',
    'background_color' => 'bg-gray-100',
    'text_color' => 'text-gray-900',
    'has_form' => true,
    'form_fields' => [
        [
            'name' => 'name',
            'label' => 'Full Name',
            'type' => 'text',
            'required' => true,
            'placeholder' => 'John Doe',
            'validation' => 'required|min:2'
        ],
        [
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'required' => true,
            'placeholder' => 'john@example.com',
            'validation' => 'required|email'
        ],
        [
            'name' => 'subject',
            'label' => 'Subject',
            'type' => 'select',
            'required' => true,
            'options' => ['General Inquiry', 'Support', 'Sales', 'Partnership'],
            'validation' => 'required'
        ],
        [
            'name' => 'message',
            'label' => 'Message',
            'type' => 'textarea',
            'required' => true,
            'placeholder' => 'Tell us how we can help...',
            'validation' => 'required|min:10'
        ]
    ],
    'form_submit_button_text' => 'Send Message',
    'form_success_message' => 'Thank you for contacting us! We\'ll respond within 24 hours.',
    'is_published' => true,
]);
```

Now visitors can access `https://yourapp.com/contact` and submit the form!

## Summary

By consolidating landing pages and forms into the CMS system, we've created a more powerful, flexible, and maintainable solution. Admin users can now create any type of page—from simple static content to complex forms—all through one unified interface.

This architecture follows the principle of "convention over configuration" while still providing extensive customization options through the form builder system.
