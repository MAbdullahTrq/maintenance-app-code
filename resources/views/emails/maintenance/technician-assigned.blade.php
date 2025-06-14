@component('mail::message')
# New Maintenance Request Assigned

Hello {{ $maintenance_request->assignedTechnician->name }},

You have been assigned to a new maintenance request:

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Due Date: {{ $maintenance_request->due_date ? $maintenance_request->due_date->format('M d, Y') : 'Not specified' }}

**Description:**
{{ $maintenance_request->description }}

@component('mail::button', ['url' => route('maintenance.show', $maintenance_request)])
View Request
@endcomponent

Please review the request and take appropriate action.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 