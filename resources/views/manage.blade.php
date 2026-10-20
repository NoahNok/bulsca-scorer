{{--
    Inertia root view for the Management app (Management and Admin, see
    docs/adr/0002-public-blade-management-svelte.md). The Digital Judge uses app.blade.php.
    Shares the v2 tokens and components (resources/css/v2.css) with the Blade v2 pages.
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <x-favicon />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="color-scheme" content="light dark">

    @include('landing.v2.partials.theme-script')

    <link rel="stylesheet" href="/fonts/google/fonts.css">
    @vite(['resources/css/app.css', 'resources/css/v2.css', 'resources/js/manage.ts'])
    <x-inertia::head>
        <title>Scoring.Events</title>
    </x-inertia::head>
</head>

<body class="v2 overflow-x-hidden">
    <x-inertia::app />
</body>

</html>
