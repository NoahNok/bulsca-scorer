<x-mail::message>
# You've been added to {{ $competition->name }}

You've been added as {{ $roleName === 'official' ? 'an' : 'a' }} {{ $roleName }} for **{{ $competition->name }}** on {{ $competition->when->format('jS F Y') }}. It will be on your Digital Judge home page when you sign in.

@if ($newAccount)
We've made a Digital Judge account for you. Sign in with this email address and we'll email you a PIN.
@endif

<x-mail::button :url="$url">
Open Digital Judge
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
