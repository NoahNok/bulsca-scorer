<x-mail::message>
# You've been added to {{ $competition->name }}

You've been added as {{ $roleName === 'official' ? 'an' : 'a' }} {{ $roleName }} for **{{ $competition->name }}** on {{ $competition->when->format('jS F Y') }}. It will be on your Digital Judge home page when you sign in.

@if ($newAccount)
We've made a Digital Judge account for you. Sign in with this email address and we'll email you a PIN.

An organiser of {{ $competition->name }} made this account for you with your name and email address. Our [Privacy Policy]({{ route('legal.privacy') }}) explains how we use them, and you can ask us to delete the account at any time.
@endif

<x-mail::button :url="$url">
Open Digital Judge
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
