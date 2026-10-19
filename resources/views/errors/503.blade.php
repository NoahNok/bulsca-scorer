{{--
    Shown while the site is in maintenance mode. deploy.sh pre-renders it with
    `artisan down --render`, so it's served without booting the app and has to
    stand alone: inline styles only, no @vite (public/build is rebuilt mid-deploy).
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- Same as <x-favicon />, but root-relative: rendered from the CLI, asset() would bake in APP_URL's scheme. --}}
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back shortly | Scoring.Events</title>

    <link rel="stylesheet" href="/fonts/google/fonts.css">

    <style>
        :root {
            --bg: #f9fafb;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --accent: #00d5be;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #030712;
                --card: #111827;
                --text: #f9fafb;
                --muted: #9ca3af;
                --border: #1f2937;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: var(--bg);
            color: var(--text);
            font-family: 'Archivo', ui-sans-serif, system-ui, sans-serif;
        }

        main {
            width: 100%;
            max-width: 28rem;
            padding: 2.5rem 2rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            text-align: center;
        }

        .logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 1.5rem;
            display: block;
        }

        h1 {
            margin: 0 0 0.75rem;
            font-size: 1.875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.01em;
        }

        p {
            margin: 0;
            line-height: 1.6;
            color: var(--muted);
        }

        .status {
            margin-top: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .dot {
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 9999px;
            background: var(--accent);
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.8); }
        }

        @media (prefers-reduced-motion: reduce) {
            .dot {
                animation: none;
            }
        }
    </style>
</head>

<body>
    <main>
        <svg class="logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true">
            <rect width="64" height="64" rx="14" fill="#111827" />
            <path d="M40 20.5C37 16 30 15 25.5 16.5S18 22 19.5 26.5C21.5 32 37.5 31 39.5 38.5C41 44 36 48.5 29.5 48.5C24 48.5 19.5 46 17.5 42.5"
                fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" />
            <circle cx="49" cy="45" r="5.5" fill="#00d5be" />
        </svg>

        <h1>Back shortly</h1>
        <p>Scoring.Events is being updated. This usually takes a minute or two.</p>

        <div class="status" role="status">
            <span class="dot" aria-hidden="true"></span>
            This page will reload when we're back
        </div>
    </main>
</body>

</html>
