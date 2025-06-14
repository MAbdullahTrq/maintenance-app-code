@component('mail::message')
# Maintenance Work Completed

Hello {{ $maintenance_request->property->manager->name }},

The maintenance request has been completed by {{ $maintenance_request->assignedTechnician->name }}:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Completed At: {{ $maintenance_request->completed_at->format('M d, Y H:i') }}

@component('mail::button', ['url' => route('maintenance.show', $maintenance_request)])
View Request
@endcomponent

@if($maintenance_request->requester_email)
A notification has also been sent to the requester.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent 