@component('mail::message')
# Maintenance Work Started

Hello {{ $maintenance_request->requester_name ?: 'there' }},

The maintenance request you submitted has been started by our technician {{ $maintenance_request->assignedTechnician->name }}:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Started At: {{ now()->format('M d, Y H:i') }}

We will keep you updated on the progress of your maintenance request.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 