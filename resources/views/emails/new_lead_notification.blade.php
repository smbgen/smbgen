<x-mail::message>
# New Lead Received

A new lead has been submitted on your website.

<x-mail::panel>
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
</x-mail::panel>

<x-mail::button :url="route('admin.leads.show', $lead)">
View Lead Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
