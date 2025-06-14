@component('mail::message')
# Update on Your Maintenance Request

Hello {{ $maintenance_request->requester_name ?: 'there' }},

Our technician {{ $maintenance_request->assignedTechnician->name }} has added an update to your maintenance request:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}

**Update from Technician:**
{{ $comment->comment }}

We will continue to keep you informed of any progress on your maintenance request.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 