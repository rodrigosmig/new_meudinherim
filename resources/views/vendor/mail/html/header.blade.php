<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ asset('images/logo.png') }}" class="logo" alt="Meu Dinherim" style="width: 20px; height: 20px">
{{ $slot }}
@endif
</a>
</td>
</tr>
