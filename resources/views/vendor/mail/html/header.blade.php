@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@elseif (
    stripos($slot, 'maintain') !== false || 
    stripos(config('app.name'), 'maintain') !== false ||
    $slot === 'MaintainXtra' ||
    $slot === 'Laravel' ||
    config('app.name') === 'MaintainXtra' ||
    config('app.name') === 'Laravel'
)
<img src="{{ config('app.url') }}/images/logo.png" class="logo" alt="MaintainXtra Logo" style="height: 60px; max-height: 60px; width: auto; max-width: 200px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
