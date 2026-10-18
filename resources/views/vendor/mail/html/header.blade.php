@props(['url'])
<tr>
<td class="header">
<table cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="header-logo">
<a href="{{ $url }}"><img src="{{ asset('email-logo.png') }}" class="logo" width="48" height="48" alt=""></a>
</td>
<td class="header-name">
<a href="{{ $url }}">{{ $slot }}</a>
</td>
</tr>
</table>
</td>
</tr>
