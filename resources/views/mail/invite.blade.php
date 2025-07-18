<x-mail::message>
# You've been invited to {{ $to }}

Click below to accept the invitation

<x-mail::button :url="$url">
    Accept
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
