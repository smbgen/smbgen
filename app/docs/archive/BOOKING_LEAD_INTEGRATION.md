# Booking → Lead Integration

## Overview
Bookings now automatically create lead form entries when submitted, treating every booking as a potential lead capture opportunity.

## Changes Made

### 1. Configuration (`config/business.php`)
Added new `booking` configuration section:

```php
'booking' => [
    'require_property_address' => env('BOOKING_REQUIRE_PROPERTY_ADDRESS', false),
    'show_property_address_field' => env('BOOKING_SHOW_PROPERTY_ADDRESS', true),
    'require_phone' => env('BOOKING_REQUIRE_PHONE', false),
    'show_phone_field' => env('BOOKING_SHOW_PHONE', true),
    'create_lead' => env('BOOKING_CREATE_LEAD', true),
],
```

**Environment Variables:**
- `BOOKING_SHOW_PROPERTY_ADDRESS` - Show property address field (default: true)
- `BOOKING_REQUIRE_PROPERTY_ADDRESS` - Make property address required (default: false)
- `BOOKING_SHOW_PHONE` - Show phone number field (default: true)
- `BOOKING_REQUIRE_PHONE` - Make phone number required (default: false)
- `BOOKING_CREATE_LEAD` - Auto-create lead from bookings (default: true)

### 2. BookingController Updates
**File:** `app/Http/Controllers/BookingController.php`

#### Dynamic Validation
- Phone and property address fields now validate based on config
- Fields only validate if they're enabled in config
- Required/optional status respects config settings

#### Lead Creation
When a booking is submitted (and `BOOKING_CREATE_LEAD=true`):

1. **Creates LeadForm record** with:
   - Name & Email from booking
   - Message (notes or auto-generated booking summary)
   - Source: `booking_system`
   - IP address, user agent, referer captured

2. **Stores booking data in `form_data` JSON field**:
   ```json
   {
     "phone": "555-123-4567",
     "property_address": "123 Main St",
     "booking_id": 42,
     "booking_date": "2025-10-15",
     "booking_time": "2:00 PM",
     "source_type": "booking"
   }
   ```

3. **Logs success/failure** without breaking booking flow
   - If lead creation fails, booking still succeeds
   - Errors logged for debugging

### 3. Booking Form Updates
**File:** `resources/views/book/wizard.blade.php`

#### Conditional Phone Field
```blade
@if(config('business.booking.show_phone_field', true))
<div class="md:col-span-2">
    <label>Phone {{ config('business.booking.require_phone', false) ? '*' : '(optional)' }}</label>
    <input name="phone" type="tel" {{ config('business.booking.require_phone', false) ? 'required' : '' }} />
</div>
@endif
```

#### Conditional Property Address Field
```blade
@if(config('business.booking.show_property_address_field', true))
<div class="mt-4">
    <label>Property Address {{ config('business.booking.require_property_address', false) ? '*' : '(optional)' }}</label>
    <textarea name="property_address" {{ config('business.booking.require_property_address', false) ? 'required' : '' }}></textarea>
</div>
@endif
```

### 4. Lead Data Mapping

| Booking Field | Lead Field | Location |
|--------------|------------|----------|
| `customer_name` | `name` | Direct |
| `customer_email` | `email` | Direct |
| `notes` | `message` | Direct (or auto-generated) |
| `customer_phone` | `form_data['phone']` | JSON |
| `property_address` | `form_data['property_address']` | JSON |
| Booking ID | `form_data['booking_id']` | JSON |
| Booking Date/Time | `form_data['booking_date']` & `['booking_time']` | JSON |
| - | `source_site` | Set to 'booking_system' |
| - | `form_data['source_type']` | Set to 'booking' |

### 5. Tests
**File:** `tests/Feature/BookingLeadCreationTest.php`

✅ **6 comprehensive tests:**
1. Creates lead form when booking submitted
2. Respects `create_lead` config toggle
3. Includes booking details in lead message
4. Respects phone field visibility config
5. Validates phone as required when configured
6. Validates property address as required when configured

**All tests passing** with 18 assertions

## Usage Examples

### Default Configuration (Recommended)
```env
BOOKING_SHOW_PROPERTY_ADDRESS=true
BOOKING_REQUIRE_PROPERTY_ADDRESS=false
BOOKING_SHOW_PHONE=true
BOOKING_REQUIRE_PHONE=false
BOOKING_CREATE_LEAD=true
```
- Shows optional phone and property address fields
- Creates leads automatically
- Great for collecting extra info without friction

### Minimal Booking Form
```env
BOOKING_SHOW_PROPERTY_ADDRESS=false
BOOKING_SHOW_PHONE=false
BOOKING_CREATE_LEAD=true
```
- Only name, email, notes fields
- Still creates leads with basic info
- Fastest checkout experience

### Required Contact Info
```env
BOOKING_SHOW_PHONE=true
BOOKING_REQUIRE_PHONE=true
BOOKING_SHOW_PROPERTY_ADDRESS=true
BOOKING_REQUIRE_PROPERTY_ADDRESS=true
BOOKING_CREATE_LEAD=true
```
- Forces collection of phone and address
- Ensures complete lead data
- Best for service businesses needing location info

### Disable Lead Creation
```env
BOOKING_CREATE_LEAD=false
```
- Bookings work normally
- No lead forms created
- Use if lead management not needed

## Benefits

✅ **Unified Lead Management** - All customer contacts in one place  
✅ **Rich Data Capture** - Phone, address, booking details preserved  
✅ **Configurable** - Enable/disable fields per business needs  
✅ **Non-Breaking** - Lead creation failure doesn't break bookings  
✅ **Searchable** - Leads show in admin leads index with full info  
✅ **Traceable** - Each lead linked back to booking ID  
✅ **Analytics Ready** - Source tracking for conversion metrics  

## Admin Experience

### Leads Index (`admin/leads`)
Booking-generated leads display:
- Customer name & email
- Phone number (if provided)
- Property address snippet
- Message/notes from booking
- Source badge: "booking_system"
- Booking date & time in message
- All standard lead actions (view, convert, delete)

### Lead Detail View
`form_data` JSON shows:
- Original booking ID
- Scheduled date & time
- Property address
- Phone number
- Source type marker

## Future Enhancements

- [ ] Link lead directly to booking (add `booking_id` column to `lead_forms`)
- [ ] Show booking status on lead detail page
- [ ] One-click navigate from lead to booking
- [ ] Duplicate detection (same email booking multiple times)
- [ ] Lead scoring based on booking behavior
- [ ] Automated follow-up sequences for booking leads
