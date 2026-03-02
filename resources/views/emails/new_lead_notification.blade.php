@component('mail::message')
# New Lead Received

A new lead has been submitted on your website.

@component('mail::panel')
**Name:** {{ $lead->name }}

**Email:** {{ $lead->email }}

@if($lead->form_data && isset($lead->form_data['phone']))
**Phone:** {{ $lead->form_data['phone'] }}
@endif

@if($lead->message)
**Message:**
{{ $lead->message }}
@endif

@if($lead->cmsPage)
**Source Page:** {{ $lead->cmsPage->title }}
@elseif($lead->source_site)
**Source:** {{ $lead->source_site }}
@endif

**Received:** {{ $lead->created_at->format('M d, Y \a\t g:i A') }}
@endcomponent

@component('mail::button', ['url' => route('admin.leads.show', $lead)])
View Lead Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
