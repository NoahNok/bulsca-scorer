<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
You're receiving this email from [{{ config('app.name') }}]({{ config('app.url') }}).<br>
[Terms]({{ route('legal.terms') }}) · [Privacy]({{ route('legal.privacy') }}) · [Contact](mailto:{{ config('legal.contact_email') }})
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
