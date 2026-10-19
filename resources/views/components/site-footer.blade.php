{{-- Links to the legal pages, at the bottom of every public page. Links are absolute so they work from the subdomains.
     The gap is inline because the standalone pages' prebuilt public/css/app.css has no gap utilities. --}}
<footer class="mt-auto w-full border-t border-gray-200 py-6 text-center text-xs text-gray-500">
    <nav class="flex flex-wrap items-center justify-center" style="column-gap: 1rem; row-gap: 0.25rem">
        <span>&copy; {{ now()->year }} {{ config('legal.trading_as') }}</span>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.terms') }}">Terms</a>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.privacy') }}">Privacy</a>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.cookies') }}">Cookies</a>
        <a class="hover:text-gray-900 hover:underline" href="mailto:{{ config('legal.contact_email') }}">Contact</a>
    </nav>
</footer>
