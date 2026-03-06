<x-mail::message>
# New Contact Form Inquiry

You have received a new inquiry from your contact form.

**Name:** {{ $name }}

**Email:** {{ $email }}

**Phone:** {{ $phone ?? 'Not provided' }}

**Preferred Contact:** {{ $preferredContact ?? 'Email' }}

**Message:**

{{ $message }}

---

**Submitted:** {{ $submittedAt }}

<x-mail::button :url="'mailto:'.$email">
Reply to {{ $name }}
</x-mail::button>

@if($phone)
<x-mail::button :url="'tel:'.$phone">
Call {{ $name }}
</x-mail::button>
@endif


This is an automated notification from your website contact form.
</x-mail::message>
