{{-- Under a form that signs someone in or gives them an account. --}}
<p {{ $attributes->merge(['class' => 'text-xs text-gray-500']) }}>
    By continuing you agree to the <a class="link" href="{{ route('legal.terms') }}">Terms of Service</a> and
    <a class="link" href="{{ route('legal.privacy') }}">Privacy Policy</a>.
</p>
