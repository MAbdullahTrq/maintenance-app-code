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
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 28px; font-weight: bold; color: #3d4852; text-align: center;">
    <span style="color: #1e40af;">Maintain</span><span style="color: #000000;">Xtra</span>
</div>
@else
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 24px; font-weight: bold; color: #3d4852;">
    <span style="color: #1e40af;">Maintain</span><span style="color: #000000;">Xtra</span>
</div>
@endif
</a>
</td>
</tr>
