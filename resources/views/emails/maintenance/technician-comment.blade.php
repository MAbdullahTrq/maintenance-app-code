@component('mail::message')
# New Comment on Maintenance Request

Hello {{ $maintenance_request->property->manager->name }},

{{ $maintenance_request->assignedTechnician->name }} has added a new comment to the maintenance request:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}

**New Comment:**
{{ $comment->comment }}

@component('mail::button', ['url' => route('maintenance.show', $maintenance_request)])
View Request
@endcomponent

@if($maintenance_request->requester_email)
A notification has also been sent to the requester.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent 