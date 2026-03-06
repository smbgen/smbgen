# ClientBridge Booking System Documentation

## 🎯 **Overview**

The ClientBridge booking system is a complete appointment scheduling solution with Google Calendar integration, blackout date management, and double-booking prevention.

## 📋 **Features**

### ✅ **Implemented Features**
- **Google Calendar Integration** - Two-way sync with Google Calendar
- **Availability Management** - Admin can set M-F working hours
- **Blackout Dates** - Block specific dates with optional reasons
- **Double-booking Prevention** - Visual strikethrough for booked slots
- **Google Meet Links** - Automatic meet link generation
- **Email Notifications** - Booking confirmations with calendar invites
- **Timezone Support** - Proper timezone conversion
- **Admin Dashboard** - Manage all bookings and availability

### 🔧 **Technical Architecture**

#### **Database Tables**
```sql
-- Core booking table
bookings (
    id, customer_name, customer_email, customer_phone,
    booking_date, booking_time, status, notes,
    google_event_id, google_meet_link,
    created_at, updated_at
)

-- Admin availability settings
availabilities (
    id, user_id, day_of_week, start_time, end_time,
    minimum_booking_notice_hours, maximum_booking_days_ahead,
    timezone, is_active, created_at, updated_at
)

-- Blackout dates
blackout_dates (
    id, user_id, date, reason, created_at, updated_at
)

-- Google OAuth tokens
users (
    id, name, email, google_refresh_token, google_calendar_id, ...
)
```

#### **Key Controllers**
- `BookingController` - Public booking interface
- `Admin\AvailabilityController` - Admin availability & blackout management
- `Admin\CalendarController` - Google Calendar OAuth flow

#### **Services**
- `GoogleCalendarService` - Google Calendar API integration
- `AvailabilityService` - Slot calculation and validation

## 🚀 **Setup Instructions**

### **1. Google Calendar Setup**
```bash
# Set up Google OAuth credentials in .env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_CALENDAR_REDIRECT_URI=https://yourdomain.com/admin/calendar/callback
```

### **2. Database Migration**
```bash
# Run migrations to create booking tables
php artisan migrate

# Seed default availability (M-F 9am-5pm)
php artisan db:seed --class=AvailabilitySeeder
```

### **3. Admin Setup**
```bash
# Connect admin's Google Calendar
1. Login as admin
2. Go to /admin/calendar
3. Click "Connect Google Calendar"
4. Complete OAuth flow
5. Select primary calendar
```

## 📅 **Usage Guide**

### **Admin - Setting Availability**

#### **Configure Working Hours**
```
/admin/availability
├── Set working days (Mon-Fri)
├── Set working hours (9:00 AM - 5:00 PM)
├── Set minimum notice (24 hours)
├── Set maximum advance booking (30 days)
└── Set timezone
```

#### **Add Blackout Dates**
```
/admin/availability (scroll down)
├── Select date from calendar picker
├── Add optional reason ("Vacation", "Conference", etc.)
├── Click "Add Blackout Date"
└── Remove dates as needed
```

### **Customer - Making Bookings**

#### **Booking Flow**
```
/book
├── 1. Select available date
├── 2. Choose available time slot
├── 3. Enter contact information
├── 4. Confirm booking
└── 5. Receive confirmation email with Google Meet link
```

## 🔧 **Artisan Commands for Deployment**

### **Tinker Commands for VPS Migration Fix**
```bash
# Fix migration conflicts on VPS
php artisan tinker

# In tinker, run these commands:
DB::table('migrations')->insert([
    'migration' => '2025_10_02_000003_add_google_calendar_columns_to_users_table', 
    'batch' => 3
]);

DB::table('migrations')->insert([
    'migration' => '2025_10_02_000004_create_blackout_dates_table', 
    'batch' => 3
]);

# Exit tinker
exit

# Verify migrations are marked as run
php artisan migrate:status
```

### **Deploy Availability Seeder (1-Hour Meetings)**
```bash
# Create M-F 9am-5pm availability for admin user
php artisan db:seed --class=AvailabilitySeeder

# Or create custom availability via tinker (1-hour meetings)
php artisan tinker

# Laravel Cloud Console Command:

## Simple Seeder (Recommended)
The AvailabilitySeeder has been updated for your Mon-Wed, 10am-4pm schedule:

```bash
php artisan db:seed --class=AvailabilitySeeder
```

### **Targeting Specific Users**
To target a specific user, edit `database/seeders/AvailabilitySeeder.php` and uncomment one of these options:

```php
// Option 1: Target by user ID
$adminUser = User::find(1); // Replace 1 with desired user ID

// Option 2: Target by email  
$adminUser = User::where('email', 'admin@example.com')->first();

// Option 3: Target first admin user
$adminUser = User::where('role', 'admin')->first();

// Option 4: Get first user (default - currently active)
$adminUser = User::first();
```

### **For Laravel Cloud Console**
If you can't edit files, use tinker to target specific user:

```bash
# Target user ID 1
php artisan tinker --execute="$user = \App\Models\User::find(1); \App\Models\Availability::where('user_id', $user->id)->delete(); foreach ([1,2,3] as $day) { \App\Models\Availability::create(['user_id' => $user->id, 'day_of_week' => $day, 'start_time' => '10:00', 'end_time' => '16:00', 'duration' => 60, 'minimum_booking_notice_hours' => 24, 'maximum_booking_days_ahead' => 30, 'timezone' => 'America/New_York', 'is_active' => true]); }"

# Target by email
php artisan tinker --execute="$user = \App\Models\User::where('email', 'admin@example.com')->first(); \App\Models\Availability::where('user_id', $user->id)->delete(); foreach ([1,2,3] as $day) { \App\Models\Availability::create(['user_id' => $user->id, 'day_of_week' => $day, 'start_time' => '10:00', 'end_time' => '16:00', 'duration' => 60, 'minimum_booking_notice_hours' => 24, 'maximum_booking_days_ahead' => 30, 'timezone' => 'America/New_York', 'is_active' => true]); }"
```

### **What the Seeder Does:**
- ✅ Clear any existing availability for the targeted user
- ✅ Create Monday, Tuesday, Wednesday availability
- ✅ Set hours: 10:00 AM - 4:00 PM Eastern Time
- ✅ Configure 60-minute slots (45min meeting + 15min buffer)
- ✅ Set 24-hour minimum booking notice
- ✅ Set 30-day maximum advance booking
- ✅ Show confirmation messages for each day created

**Result:** 6 booking slots per day × 3 days = 18 total weekly slots **for that specific user**
```

## 🎯 **Key URLs**

### **Public URLs**
- `/book` - Public booking interface
- `/booking/{id}/confirmation` - Booking confirmation page

### **Admin URLs**
- `/admin/availability` - Manage availability & blackout dates
- `/admin/calendar` - Google Calendar connection
- `/admin/calendar/select` - Select Google Calendar
- `/admin/bookings` - View all bookings (if implemented)

## 🐛 **Troubleshooting**

### **Common Issues**

#### **"No available slots" showing**
```bash
# Check if availability is set
php artisan tinker
\App\Models\Availability::where('user_id', 1)->get();

# Check for blackout dates
\App\Models\BlackoutDate::all();

# Check timezone settings
config('app.timezone');
```

#### **Google Calendar not syncing**
```bash
# Check OAuth tokens
php artisan tinker
User::first()->google_refresh_token; // Should not be null

# Test Google Calendar service
$service = app(\App\Services\GoogleCalendarService::class);
$service->listCalendars();
```

#### **Bookings not appearing in Google Calendar**
```bash
# Check Google Calendar ID is set
User::first()->google_calendar_id; // Should not be null

# Check event creation in logs
tail -f storage/logs/laravel.log | grep "Google"
```

## 📊 **Database Queries for Analytics**

```sql
-- Total bookings this month
SELECT COUNT(*) FROM bookings 
WHERE booking_date >= DATE_FORMAT(NOW(), '%Y-%m-01');

-- Popular booking times
SELECT booking_time, COUNT(*) as count 
FROM bookings 
GROUP BY booking_time 
ORDER BY count DESC;

-- Conversion rate (if you track page views)
SELECT 
    (SELECT COUNT(*) FROM bookings) as bookings,
    -- Add page view tracking for full conversion rate
```

## 🔄 **Feature Flags**

The booking system respects these feature flags in `config/business.php`:

```php
'features' => [
    'booking_system' => true,        // Enable/disable booking
    'google_calendar' => true,       // Enable Google Calendar sync
    'blackout_dates' => true,        // Enable blackout date management
    'home_page' => false,           // Show home page or redirect to /book
]
```

## 🚀 **Next Enhancements (Roadmap)**

- [ ] **Customer Account System** - Let customers manage their own bookings
- [ ] **Payment Integration** - Stripe/PayPal for paid bookings
- [ ] **Recurring Appointments** - Weekly/monthly recurring bookings
- [ ] **Buffer Times** - Automatic breaks between appointments
- [ ] **Multiple Staff** - Support for multiple calendar users
- [ ] **Booking Types** - Different appointment types (30min, 60min, etc.)
- [ ] **Waiting List** - Queue customers for cancelled slots
- [ ] **SMS Notifications** - Text message confirmations

---

*Last Updated: October 3, 2025*
*Status: Fully Functional - Ready for Production*