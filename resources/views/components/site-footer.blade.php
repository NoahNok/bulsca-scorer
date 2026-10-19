{{-- Links to the legal pages, at the bottom of every public page. Links are absolute so they work from the subdomains. --}}
<footer class="mt-auto w-full border-t border-gray-200 py-6 text-center text-xs text-gray-500">
    <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
        <span>&copy; {{ now()->year }} {{ config('legal.trading_as') }}</span>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.terms') }}">Terms</a>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.privacy') }}">Privacy</a>
        <a class="hover:text-gray-900 hover:underline" href="{{ route('legal.cookies') }}">Cookies</a>
        <a class="hover:text-gray-900 hover:underline" href="mailto:{{ config('legal.contact_email') }}">Contact</a>
    </nav>
</footer>
