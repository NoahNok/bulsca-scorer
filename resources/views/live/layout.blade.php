<!DOCTYPE html>
<html lang="en">

<head>
    @include('live.partials.head', ['title' => trim($__env->yieldContent('title')) ?: ($comp ?? null)?->name ?? 'Live'])
</head>

<body class="flex min-h-dvh flex-col overflow-x-hidden selection:bg-se selection:text-white" x-data>

    <header class="bg-white z-10 shadow-sm">
        <nav class="mx-auto flex items-center justify-between p-4 lg:px-8">
            <a href="{{ \App\Helpers\RouteHelpers::mainRoute('explore') }}">
                <x-se-title />
            </a>

            @isset($comp)
                @include('live.partials.status')
            @endisset
        </nav>
    </header>

    <main class="flex w-screen justify-center my-8 md:my-12">
        <div class="w-[90%] lg:w-[85%] 3xl:w-[65%] sm:px-6 md:px-8 flex flex-col">

            @isset($comp)
                @include('live.partials.store')

                <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0">
                    <div>
                        <h1 class="xl:text-5xl!">{{ $comp->name }}</h1>
                        <p class="font-archivo text-sm! text-gray-700! uppercase -mb-1">{{ $comp->when->format('M jS Y') }}
                            · {{ $comp->where }}</p>
                    </div>

                    @if ($comp->getOrganisation)
                        <img src="{{ $comp->getOrganisation->getLogo() }}" alt="" class="size-16 rounded-full mb-4 md:mb-0">
                    @endif
                </div>

                <div class="tabbed-bar mt-4 mb-6">
                    <a href="{{ route('live', request()->all()) }}"
                        class="@if (Route::currentRouteName() == 'live') active @endif">Live</a>
                    <a href="{{ route('live.dqs', request()->all()) }}"
                        class="@if (Str::startsWith(Route::currentRouteName(), 'live.dqs')) active @endif">DQs & Penalties</a>
                    <a href="{{ route('live.display', request()->all()) }}" target="_blank">Full screen &nearr;</a>
                    <a href="{{ \App\Helpers\RouteHelpers::mainRoute('landing.competition.heats-draws', $comp->getSlug()) }}">Heats & Draws &nearr;</a>
                </div>
            @endisset

            @yield('content')
        </div>
    </main>

    <x-site-footer />
</body>

</html>
