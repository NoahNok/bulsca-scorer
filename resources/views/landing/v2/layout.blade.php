{{--
    Shell for redesigned (v2) pages. Standalone rather than layouts.core because these pages follow
    the OS dark mode and core's header and footer are light-only. Styles live in resources/css/v2.css.
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <x-favicon />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>{{ trim(View::yieldContent('title')) ? trim(View::yieldContent('title')) . ' | ' : '' }}Scoring.Events</title>
    @yield('v2-meta')

    @include('landing.v2.partials.theme-script')
    @include('landing.v2.partials.smooth-height-script')

    <link rel="stylesheet" href="/fonts/google/fonts.css">
    @vite('resources/js/basic-app.js')
    @vite('resources/css/app.css')
    {{-- The shared v2 tokens and components (docs/v2-ui-style-guide.md) --}}
    @vite('resources/css/v2.css')
    {{-- Plugins load before Alpine itself; focus gives modals x-trap --}}
    <script defer src="/vendor/alpinejs-focus@3.13.3/cdn.min.js"></script>
    <script defer src="/vendor/alpinejs@3.13.3/cdn.min.js"></script>

    {{--
        Moving between these pages animates like a Management app visit: v2.css's page fade, with the header and
        footer held still. Here rather than in v2.css, which the Management app loads too. Browsers without
        cross-document view transitions just load the page.
    --}}
    <style>
        @view-transition {
            navigation: auto;
        }
    </style>

    {{--
        Fetch a public page as soon as a link to it is hovered or pressed, so the click opens it from cache.
        Prefetch only, not prerender, so no page's scripts run before it's opened. Same-origin links only;
        data-no-prefetch opts a link out. Browsers without speculation rules ignore this.
    --}}
    <script type="speculationrules">
        {
            "prefetch": [{
                "where": {
                    "and": [
                        { "href_matches": "/*" },
                        { "not": { "selector_matches": "[data-no-prefetch], [download], [target=_blank]" } }
                    ]
                },
                "eagerness": "moderate"
            }]
        }
    </script>

    @yield('v2-styles')
</head>

<body class="v2 flex min-h-dvh flex-col overflow-x-hidden selection:bg-se selection:text-white">
    <header class="v2-header">
        <a href="{{ route('home') }}" class="v2-brand">
            @include('landing.v2.partials.mark', ['size' => 32])
            Scoring.Events
        </a>
        <nav class="v2-nav" aria-label="Main">
            <a href="{{ route('explore') }}" class="v2-nav-wide">Explore</a>
            <a href="{{ route('dj.home') }}" class="v2-nav-wide">Officials</a>
            {{-- The main domain has no session (ADR 0002): app.'s home sends anyone signed in straight to Management --}}
            <a href="{{ route('manage.home') }}">Sign in</a>
            {{-- Flips between light and dark, starting from whatever is showing (the OS setting until it's used) --}}
            {{-- window.v2Theme is resources/js/lib/theme.ts, shared with the Management app's toggle --}}
            <button type="button" class="v2-theme-toggle" x-data="{
                dark: false,
                init() { this.dark = v2Theme.isDark(); },
                flip() { v2Theme.flipTheme(this.$el, (dark) => this.dark = dark); }
            }" @click="flip()" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                aria-label="Switch theme">
                {{-- Shows the mode you'd switch to --}}
                <svg x-show="dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                </svg>
                <svg x-show="!dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" />
                </svg>
            </button>
        </nav>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="v2-footer">
        <nav aria-label="Legal">
            <span>&copy; {{ now()->year }} {{ config('legal.trading_as') }}</span>
            <a href="{{ route('legal.terms') }}">Terms</a>
            <a href="{{ route('legal.privacy') }}">Privacy</a>
            <a href="{{ route('legal.cookies') }}">Cookies</a>
            <a href="mailto:{{ config('legal.contact_email') }}">Contact</a>
        </nav>
    </footer>

    @include('landing.v2.partials.notice-toast')
</body>

</html>
