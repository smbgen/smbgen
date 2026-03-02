# 📅 Booking Manager Widget

## Overview
A comprehensive, smart booking management widget that provides quick access to all booking-related features from the dashboard.

## Features

### 📊 Real-time Stats
- **Pending Bookings** - Bookings awaiting confirmation
- **Upcoming Bookings** - All confirmed future appointments  
- **This Week** - Total bookings for the current week

### 🔗 Google Calendar Integration
- **Connected Status** - Visual indicator with animated pulse
- **One-Click Connect** - Direct link to Google OAuth flow
- **Disconnect Option** - Easy way to unlink calendar
- **Sync Notifications** - Helpful messages about calendar sync status

### ⚡ Quick Actions
1. **Connect/Disconnect Google Calendar** - Manage calendar integration
2. **Availability Settings** - Configure your booking availability
3. **View All Bookings** - Smooth scroll to bookings table
4. **Public Booking Page** - Opens your client-facing booking form

### 🌐 Shareable Booking URL
- **Copy to Clipboard** - One-click copy with visual feedback
- **Direct Link** - Full URL displayed for easy sharing
- **Success Animation** - Shows "Copied!" confirmation

### 📝 Recent Activity
- Shows last 3 bookings with:
  - Customer name
  - Time ago (human-readable)
- Empty state when no bookings exist

### 🎨 Visual Design
- **Gradient Background** - Blue to cyan gradient for prominence
- **Status Badges** - Color-coded connection status
  - 🟢 Green pulse = Connected
  - 🔴 Red badge = Not connected
- **Info Panels**
  - 🟢 Green = Calendar sync active
  - 🟡 Yellow = Calendar not connected warning
- **Glassmorphism** - Modern backdrop blur effects
- **Responsive Grid** - Adapts to all screen sizes

## Location
The widget appears **below Business Metrics** and **above the three-column layout** on the admin dashboard.

## Conditional Display
- Only shows if `config('business.features.appointments')` is enabled
- Gracefully handles missing Google Calendar credentials

## Smart Features

### Auto-Detection
- Automatically checks Google Calendar connection status
- Queries booking counts in real-time
- Shows relevant warnings and suggestions

### User Guidance
When calendar is **not connected**:
- Shows warning badge
- Displays help text about benefits
- Prominent "Connect" button

When calendar **is connected**:
- Shows success indicator with animation
- Confirms sync is working
- Link to manage calendar settings

### Empty States
- Shows helpful message when no bookings exist
- Suggests sharing the booking page
- Large icon for visual clarity

## Code Structure

### Service Method
```php
DashboardWidgetService::getBookingManagerData()
```
Returns:
```php
[
    'enabled' => true/false,
    'googleConnected' => true/false,
    'stats' => [
        'pending' => int,
        'upcoming' => int,
        'thisWeek' => int,
        'recentActivity' => [
            ['message' => '...', 'time' => '...']
        ]
    ]
]
```

### Component
```blade
<x-dashboard.booking-manager 
    :bookingStats="$stats" 
    :googleConnected="$connected" 
/>
```

## Routes Used
- `booking.wizard` - Public booking page
- `admin.calendar.redirect` - Google OAuth start
- `admin.calendar.disconnect` - Disconnect calendar
- `admin.calendar.index` - Calendar settings
- `admin.availability.index` - Availability configuration

## JavaScript Features
- Copy to clipboard functionality
- Success state animation
- Smooth scroll to bookings section (#bookings)

## User Benefits
1. **Single Source of Truth** - All booking info in one place
2. **Quick Setup** - Connect calendar with one click
3. **Easy Sharing** - Copy booking URL instantly
4. **At-a-Glance Overview** - See booking status immediately
5. **Smart Notifications** - Get alerts about pending items
6. **Guided Experience** - Clear next steps when not configured

## Future Enhancements
- [ ] Add calendar sync status indicator
- [ ] Show next upcoming appointment
- [ ] Add quick booking creation form
- [ ] Display booking conflicts
- [ ] Show availability percentage
- [ ] Add booking analytics (conversion rate, etc.)
