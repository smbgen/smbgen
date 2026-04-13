# Email Deliverability & Tracking Implementation Plan

**Created:** January 2025  
**Priority:** HIGH (Tony incident shows critical gap)  
**Timeline:** 3-5 days

---

## 🚨 Current Problems

### **Issue 1: Email Trust (SPF/DKIM/DMARC)**
**Symptom:** Google shows "If you don't know [sender]..." warning
**Impact:** Recipients distrust emails, mark as spam
**Root Cause:** 
- No SPF record configured for rtsenviro.com
- No DKIM signing on outbound emails
- No DMARC policy

### **Issue 2: Zero Visibility**
**Symptom:** Don't know if emails were sent, delivered, opened, or clicked
**Impact:** Can't troubleshoot when customers say "I never got it"
**Root Cause:**
- No email tracking system
- No delivery confirmation
- No open/click tracking

### **Issue 3: Google Calendar Event Disconnect**
**Symptom:** Calendar invites sent separately from our email system
**Impact:** We don't know if invites were received/accepted
**Root Cause:**
- Google Calendar API sends invites independently
- No webhook to track RSVP status
- Cancellations don't trigger our emails

### **Issue 4: No Admin Awareness**
**Symptom:** Admin has no dashboard to see email health
**Impact:** Silent failures, customers ghost because they never got emails
**Root Cause:**
- No centralized email monitoring
- No alerts for bounces/failures
- No "last 24 hours" email summary

---

## ✅ Solution Architecture

### **Phase 1: Email Authentication (DNS Setup)**

**What to implement:**
1. SPF record for rtsenviro.com
2. DKIM signing via NixiHost
3. DMARC policy with reporting

**Benefits:**
- ✅ Emails won't show spam warnings
- ✅ Higher inbox placement rate
- ✅ Professional sender reputation

**Technical Steps:**
```dns
; Add to rtsenviro.com DNS (NixiHost control panel)

; SPF Record (allows NixiHost to send on your behalf)
@ TXT "v=spf1 include:rtsenviro.com ~all"

; DKIM Record (get from NixiHost)
default._domainkey TXT "v=DKIM1; k=rsa; p=[PUBLIC_KEY_FROM_NIXIHOST]"

; DMARC Policy (start with monitoring)
_dmarc TXT "v=DMARC1; p=none; rua=mailto:dmarc@rtsenviro.com; pct=100"
```

**Timeline:** 1 day (DNS propagation)

---

### **Phase 2: Email Tracking System**

**Database Schema:**
```sql
CREATE TABLE email_logs (
    id INTEGER PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    booking_id INTEGER REFERENCES bookings(id) NULL,
    
    -- Email details
    to_email VARCHAR(255),
    cc_email TEXT NULL,
    subject VARCHAR(500),
    body TEXT,
    
    -- Status tracking
    status VARCHAR(50) DEFAULT 'pending', 
        -- pending, sent, delivered, bounced, failed, opened, clicked
    
    -- Delivery tracking
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    bounced_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    
    -- Error tracking
    error_message TEXT NULL,
    smtp_response TEXT NULL,
    
    -- Engagement tracking
    open_count INTEGER DEFAULT 0,
    click_count INTEGER DEFAULT 0,
    last_opened_at TIMESTAMP NULL,
    last_clicked_at TIMESTAMP NULL,
    
    -- Metadata
    tracking_id VARCHAR(100) UNIQUE, -- UUID for pixel tracking
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Index for fast queries
CREATE INDEX idx_email_logs_status ON email_logs(status);
CREATE INDEX idx_email_logs_to_email ON email_logs(to_email);
CREATE INDEX idx_email_logs_sent_at ON email_logs(sent_at);
CREATE INDEX idx_email_logs_tracking_id ON email_logs(tracking_id);
```

**Why this schema:**
- Track every email sent through the system
- Know exactly when emails were opened/clicked
- Debug bounce/failure issues
- Link emails to bookings for context
- Store SMTP responses for troubleshooting

**Timeline:** 2 hours

---

### **Phase 3: Tracking Implementation**

#### **3.1: Tracking Pixel (Email Opens)**

**How it works:**
1. Insert 1x1 transparent pixel in every email
2. Pixel URL includes unique tracking ID
3. When email opened, browser loads pixel
4. We log the open event

**Implementation:**
```php
// app/Services/EmailTrackingService.php

public function addTrackingPixel(string $htmlBody, string $trackingId): string
{
    $pixelUrl = route('email.track.open', ['id' => $trackingId]);
    
    $pixel = "<img src=\"{$pixelUrl}\" width=\"1\" height=\"1\" alt=\"\" />";
    
    // Insert before closing </body> tag
    return str_replace('</body>', $pixel . '</body>', $htmlBody);
}
```

**Route:**
```php
// routes/web.php (public route, no auth)
Route::get('/track/email/{id}', [EmailTrackingController::class, 'trackOpen'])
    ->name('email.track.open');
```

**Controller:**
```php
public function trackOpen(Request $request, string $trackingId)
{
    $emailLog = EmailLog::where('tracking_id', $trackingId)->first();
    
    if ($emailLog) {
        $emailLog->update([
            'status' => 'opened',
            'opened_at' => $emailLog->opened_at ?? now(),
            'open_count' => $emailLog->open_count + 1,
            'last_opened_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
    
    // Return transparent 1x1 GIF
    return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
        ->header('Content-Type', 'image/gif')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
}
```

**Privacy Note:** GDPR compliant (just tracks opens, no PII beyond email address)

#### **3.2: Link Tracking (Email Clicks)**

**How it works:**
1. Replace all links in email with tracking URLs
2. Tracking URL logs the click
3. Redirect to original destination

**Implementation:**
```php
public function addLinkTracking(string $htmlBody, string $trackingId): string
{
    $pattern = '/<a\s+(?:[^>]*?\s+)?href="([^"]*)"/i';
    
    return preg_replace_callback($pattern, function ($matches) use ($trackingId) {
        $originalUrl = $matches[1];
        
        // Skip tracking for unsubscribe/preferences links
        if (str_contains($originalUrl, 'unsubscribe') || str_contains($originalUrl, 'preferences')) {
            return $matches[0];
        }
        
        $trackingUrl = route('email.track.click', [
            'id' => $trackingId,
            'url' => base64_encode($originalUrl)
        ]);
        
        return str_replace($originalUrl, $trackingUrl, $matches[0]);
    }, $htmlBody);
}
```

**Route:**
```php
Route::get('/track/click/{id}', [EmailTrackingController::class, 'trackClick'])
    ->name('email.track.click');
```

**Controller:**
```php
public function trackClick(Request $request, string $trackingId)
{
    $originalUrl = base64_decode($request->query('url'));
    
    $emailLog = EmailLog::where('tracking_id', $trackingId)->first();
    
    if ($emailLog) {
        $emailLog->update([
            'status' => 'clicked',
            'clicked_at' => $emailLog->clicked_at ?? now(),
            'click_count' => $emailLog->click_count + 1,
            'last_clicked_at' => now(),
        ]);
    }
    
    return redirect($originalUrl);
}
```

#### **3.3: Send-Time Logging**

**Integration with Mail system:**
```php
// app/Mail/BookingConfirmation.php

use App\Models\EmailLog;
use Illuminate\Support\Str;

class BookingConfirmation extends Mailable
{
    public function build()
    {
        $trackingId = Str::uuid()->toString();
        
        // Log email as pending
        EmailLog::create([
            'user_id' => $this->booking->user_id,
            'booking_id' => $this->booking->id,
            'to_email' => $this->booking->client_email,
            'subject' => "Booking Confirmation - {$this->booking->start_time->format('M j, Y')}",
            'body' => $this->render(),
            'status' => 'pending',
            'tracking_id' => $trackingId,
        ]);
        
        return $this->view('emails.booking-confirmation')
                    ->with('trackingId', $trackingId);
    }
}
```

**After-send hook:**
```php
// app/Providers/EventServiceProvider.php

use Illuminate\Mail\Events\MessageSent;

protected $listen = [
    MessageSent::class => [
        SendEmailSuccessListener::class,
    ],
];
```

```php
// app/Listeners/SendEmailSuccessListener.php

public function handle(MessageSent $event)
{
    // Extract tracking ID from message
    // Update EmailLog status to 'sent'
    
    $trackingId = $this->extractTrackingId($event->message);
    
    EmailLog::where('tracking_id', $trackingId)->update([
        'status' => 'sent',
        'sent_at' => now(),
    ]);
}
```

**Timeline:** 4 hours

---

### **Phase 4: Admin Dashboard**

**Dashboard Features:**

#### **4.1: Email Health Overview**
```
┌─────────────────────────────────────────┐
│  📧 Email Deliverability (Last 24 hrs) │
├─────────────────────────────────────────┤
│  Sent: 47                               │
│  Delivered: 45 (95.7%)                  │
│  Opened: 32 (71.1%)                     │
│  Clicked: 18 (40.0%)                    │
│  Bounced: 2 (4.3%)                      │
│  Failed: 0 (0%)                         │
├─────────────────────────────────────────┤
│  🚨 Needs Attention: 2 bounces          │
│  [View Details]                         │
└─────────────────────────────────────────┘
```

#### **4.2: Recent Email Activity**
```
┌─────────────────────────────────────────────────────┐
│  Recent Emails                                      │
├──────────┬──────────────────┬──────────┬───────────┤
│ Time     │ To               │ Subject  │ Status    │
├──────────┼──────────────────┼──────────┼───────────┤
│ 2:34 PM  │ tony@example.com │ Booking  │ ✅ Opened │
│ 1:15 PM  │ jane@example.com │ Reminder │ 📤 Sent   │
│ 12:05 PM │ bob@example.com  │ Invoice  │ ❌ Bounced│
└──────────┴──────────────────┴──────────┴───────────┘
```

#### **4.3: Per-Email Detail View**
```
Email Details: Booking Confirmation
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
To: tony@example.com
Subject: Your Virtual Consultation - Jan 15

Timeline:
✅ Sent: 2:34:12 PM (via NixiHost SMTP)
✅ Delivered: 2:34:15 PM (3s delay)
✅ Opened: 2:47:03 PM (12m later)
   - IP: 192.168.1.1
   - Device: iPhone (Safari)
   - Location: Baltimore, MD
✅ Clicked: 2:47:15 PM
   - Link: Google Meet join link

Engagement:
- Opens: 3 times
- Last opened: 3:15 PM (checked twice more)
- Clicks: 1 time

SMTP Response:
250 2.0.0 OK 1234567890 - gsmtp
```

**Implementation:**
```php
// routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/emails', [AdminEmailController::class, 'index'])
        ->name('admin.emails.index');
    
    Route::get('/admin/emails/{emailLog}', [AdminEmailController::class, 'show'])
        ->name('admin.emails.show');
});
```

**Timeline:** 3 hours

---

### **Phase 5: Google Calendar Event Tracking**

**Problem:** We send invites via Google Calendar, but don't know if they're accepted.

**Solution:** Poll Google Calendar API for RSVP status

**Implementation:**
```php
// app/Services/GoogleCalendarService.php

public function getEventAttendeeStatus(string $eventId): array
{
    $event = $this->calendar->events->get('primary', $eventId);
    
    $attendees = [];
    foreach ($event->getAttendees() as $attendee) {
        $attendees[] = [
            'email' => $attendee->getEmail(),
            'response_status' => $attendee->getResponseStatus(), // 'accepted', 'declined', 'tentative', 'needsAction'
            'comment' => $attendee->getComment(),
        ];
    }
    
    return $attendees;
}
```

**Scheduled job:**
```php
// app/Console/Commands/SyncCalendarRSVPs.php

public function handle()
{
    $upcomingBookings = Booking::where('start_time', '>', now())
        ->where('start_time', '<', now()->addDays(7))
        ->whereNotNull('google_event_id')
        ->get();
    
    foreach ($upcomingBookings as $booking) {
        $attendees = app(GoogleCalendarService::class)
            ->getEventAttendeeStatus($booking->google_event_id);
        
        // Update booking with RSVP status
        $booking->update([
            'rsvp_status' => $attendees[0]['response_status'] ?? 'needsAction',
            'rsvp_updated_at' => now(),
        ]);
        
        // Alert admin if declined
        if ($attendees[0]['response_status'] === 'declined') {
            // Send notification to admin
            $booking->user->notify(new BookingDeclined($booking));
        }
    }
}
```

**Database migration:**
```php
Schema::table('bookings', function (Blueprint $table) {
    $table->string('rsvp_status')->default('needsAction')
        ->after('google_event_id');
        // needsAction, accepted, declined, tentative
    
    $table->timestamp('rsvp_updated_at')->nullable()
        ->after('rsvp_status');
});
```

**Schedule in kernel:**
```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    $schedule->command('calendar:sync-rsvps')
        ->everyFiveMinutes();
}
```

**Timeline:** 2 hours

---

### **Phase 6: Bounce & Failure Handling**

**Webhook from NixiHost (if available):**
```php
// routes/api.php (no auth - webhook)
Route::post('/webhooks/email-bounce', [WebhookController::class, 'emailBounce']);
```

**If no webhook, poll via IMAP:**
```php
// Check bounce mailbox for NDRs (Non-Delivery Reports)

public function checkBounces()
{
    $imap = new ImapClient([
        'host' => 'rtsenviro.com',
        'port' => 993,
        'encryption' => 'ssl',
        'username' => 'bounces@rtsenviro.com',
        'password' => env('BOUNCE_MAILBOX_PASSWORD'),
    ]);
    
    $messages = $imap->getUnreadMessages();
    
    foreach ($messages as $message) {
        // Parse bounce reason
        // Update EmailLog
        EmailLog::where('to_email', $this->extractBouncedEmail($message))
            ->where('status', 'sent')
            ->update([
                'status' => 'bounced',
                'bounced_at' => now(),
                'error_message' => $this->parseErrorMessage($message),
            ]);
    }
}
```

**Timeline:** 2 hours

---

## 🎯 Implementation Order

### **Day 1: Database & Basic Tracking**
- [ ] Create `email_logs` migration
- [ ] Create `EmailLog` model
- [ ] Add tracking to existing Mail classes
- [ ] Test tracking in local environment

### **Day 2: Pixel & Link Tracking**
- [ ] Build `EmailTrackingService`
- [ ] Add tracking routes (pixel, click)
- [ ] Build `EmailTrackingController`
- [ ] Test pixel loads and link redirects

### **Day 3: Admin Dashboard**
- [ ] Build email health overview card
- [ ] Build recent emails list
- [ ] Build email detail page
- [ ] Add to admin dashboard

### **Day 4: Google Calendar RSVP Tracking**
- [ ] Add RSVP fields to bookings table
- [ ] Build calendar RSVP sync command
- [ ] Schedule command to run every 5 min
- [ ] Add RSVP status to booking detail page

### **Day 5: DNS & Deliverability**
- [ ] Configure SPF record with NixiHost
- [ ] Set up DKIM signing
- [ ] Add DMARC policy
- [ ] Test email authentication (mail-tester.com)

---

## 📊 Success Metrics

**After implementation, we should see:**

### **Week 1:**
- ✅ 100% of emails tracked
- ✅ Admin can see all email activity
- ✅ Know exactly when Tony opens emails

### **Week 2:**
- ✅ Email open rate > 70%
- ✅ Inbox placement > 95%
- ✅ Zero "unknown sender" warnings

### **Week 3:**
- ✅ RSVP tracking working for all bookings
- ✅ Alerts when bookings declined
- ✅ Bounce rate < 2%

---

## 🛡️ Privacy & Compliance

**GDPR Considerations:**
- ✅ Tracking pixels are standard practice
- ✅ Only track opens/clicks, not browsing behavior
- ✅ IP addresses stored temporarily (30 days)
- ✅ Add privacy policy mention of email tracking
- ✅ Unsubscribe links bypass tracking
- ✅ Data retention policy (delete logs after 1 year)

**CAN-SPAM Compliance:**
- ✅ All emails have physical address
- ✅ Clear unsubscribe mechanism
- ✅ Honest subject lines
- ✅ Identify as advertisement (if marketing)

---

## 🔧 Tools & Resources

**Testing:**
- [mail-tester.com](https://www.mail-tester.com/) - Email deliverability score
- [mxtoolbox.com](https://mxtoolbox.com/) - DNS/SPF/DKIM checker
- [Google Postmaster](https://postmaster.google.com/) - Gmail delivery insights

**Libraries:**
- `webklex/php-imap` - IMAP bounce checking
- Laravel Mail - Built-in tracking events
- Laravel Queue - Async email sending

**Documentation:**
- [Google Calendar API](https://developers.google.com/calendar/api/v3/reference)
- [RFC 5321](https://tools.ietf.org/html/rfc5321) - SMTP Protocol
- [RFC 6376](https://tools.ietf.org/html/rfc6376) - DKIM

---

## 💡 Future Enhancements

**Phase 7 (Optional):**
- Email warmup for new domains
- A/B testing subject lines
- Send time optimization
- Re-send to unopened after 24 hours
- SMS fallback for critical emails
- WhatsApp integration

---

**Document Owner:** Development Team  
**Last Updated:** January 2025  
**Next Review:** After Phase 5 completion
