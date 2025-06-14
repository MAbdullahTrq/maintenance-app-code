@component('mail::message')
# New Maintenance Request Submitted

Hello {{ $maintenance_request->property->manager->name }},

A new maintenance request has been submitted for your property:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Property: {{ $maintenance_request->property->name }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Submitted by: {{ $maintenance_request->requester_name ?: 'Anonymous' }}
@if($maintenance_request->requester_email)
- Contact Email: {{ $maintenance_request->requester_email }}
@endif
@if($maintenance_request->requester_phone)
- Contact Phone: {{ $maintenance_request->requester_phone }}
@endif

**Description:**
{{ $maintenance_request->description }}

@component('mail::button', ['url' => route('mobile.request.show', $maintenance_request)])
View Request
@endcomponent

Please review and take appropriate action.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 