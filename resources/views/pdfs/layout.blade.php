{{--
    Shared shell for every Pack. The page header and footer come from Chrome's header/footer
    templates (pdfs.partials.header/footer), so pages here only lay out content.

    - .pack-section starts a new page (each Pool, event or SERC).
    - .pack-block is never split across pages; blocks flow until a page is full.
    - .pack-page puts one block on each page.
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    {{-- Chrome renders this from a string, so relative URLs (fonts, images) need the site as a base --}}
    <base href="{{ url('/') }}/">
    <title>{{ $packName }}</title>

    @if (Vite::isRunningHot())
        @vite('resources/css/app.css')
    @else
        <style>{!! Vite::content('resources/css/app.css') !!}</style>
    @endif

    <style>
        .pack-section {
            break-before: page;
        }

        .pack-block {
            break-inside: avoid;
        }

        .pack-page {
            break-before: page;
        }

        .pack-section > .pack-page:first-child {
            break-before: auto;
        }

        thead {
            display: table-header-group;
        }

        tr {
            break-inside: avoid;
        }
    </style>
</head>

<body class="bg-white text-black">

    <div class="min-h-[250mm] flex flex-col items-center justify-center text-center">
        <h1>{{ $packName }}</h1>
        <h3>{{ $comp->name }}</h3>
        <p>{{ $comp->where }}<br>{{ $comp->when->format('jS F Y') }}</p>
        <br>
        <br>
        @yield('cover')
    </div>

    @yield('content')

</body>

</html>
