@component('mail::message')
# New Comment on Maintenance Request

Hello,

A new comment has been added to maintenance request "{{ $maintenance_request->title }}":

**Comment by {{ $comment->user->name }}:**
{{ $comment->comment }}

**Request Details:**
- Title: {{ $maintenance_request->title }}
- Property: {{ $maintenance_request->property->name }}
- Location: {{ $maintenance_request->location }}
- Priority: {{ ucfirst($maintenance_request->priority) }}
- Status: {{ ucfirst($maintenance_request->status) }}

@component('mail::button', ['url' => route('mobile.technician.request.show', $maintenance_request)])
View Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 