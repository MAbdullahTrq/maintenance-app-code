@component('mail::message')
# Maintenance Work Completed

Hello {{ $maintenance_request->requester_name ?: 'there' }},

Great news! The maintenance request you submitted has been completed by our technician {{ $maintenance_request->assignedTechnician->name }}:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Completed At: {{ $maintenance_request->completed_at->format('M d, Y H:i') }}

Thank you for using our maintenance service. If you have any questions or concerns about the completed work, please contact the property manager.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 