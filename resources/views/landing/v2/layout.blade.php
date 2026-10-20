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
    <meta name="robots" content="noindex">
    <meta name="color-scheme" content="light dark">
    <title>Scoring.Events</title>

    {{-- Apply a theme picked with the toggle before anything paints, so the page doesn't flash the other one --}}
    <script>
        try {
            const theme = localStorage.getItem('v2-theme');
            if (theme === 'light' || theme === 'dark') document.documentElement.dataset.theme = theme;
        } catch (e) {}
    </script>

    <link rel="stylesheet" href="/fonts/google/fonts.css">
    @vite('resources/js/basic-app.js')
    @vite('resources/css/app.css')
    {{-- The shared v2 tokens and components (docs/v2-ui-style-guide.md) --}}
    @vite('resources/css/v2.css')
    {{-- Plugins load before Alpine itself; focus gives modals x-trap --}}
    <script defer src="/vendor/alpinejs-focus@3.13.3/cdn.min.js"></script>
    <script defer src="/vendor/alpinejs@3.13.3/cdn.min.js"></script>

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
            @auth
                <a href="{{ route('home') }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}">Sign in</a>
            @endauth
            {{-- Flips between light and dark, starting from whatever is showing (the OS setting until it's used) --}}
            <button type="button" class="v2-theme-toggle" x-data="{
                dark: false,
                init() {
                    const forced = document.documentElement.dataset.theme;
                    this.dark = forced ? forced === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
                },
                flip() {
                    let applied = false;
                    const apply = () => {
                        if (applied) return;
                        applied = true;
                        this.dark = !this.dark;
                        const theme = this.dark ? 'dark' : 'light';
                        document.documentElement.dataset.theme = theme;
                        try { localStorage.setItem('v2-theme', theme); } catch (e) {}
                    };
                    if (!document.startViewTransition || matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        return apply();
                    }
                    // Reveal the new theme in a circle growing out of the toggle, big enough to reach the far corner
                    const box = this.$el.getBoundingClientRect();
                    const x = box.left + box.width / 2;
                    const y = box.top + box.height / 2;
                    const r = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
                    // If the browser never gets round to the swap (e.g. a hidden tab), make it anyway
                    setTimeout(apply, 400);
                    document.startViewTransition(apply).ready.then(() => {
                        document.documentElement.animate(
                            { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${r}px at ${x}px ${y}px)`] },
                            { duration: 500, easing: 'cubic-bezier(0.4, 0, 0.2, 1)', pseudoElement: '::view-transition-new(root)' }
                        );
                    });
                }
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
</body>

</html>
