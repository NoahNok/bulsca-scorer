{{-- Full-screen SERC order for a poolside TV or projector. No header or footer, and it never scrolls. --}}
<!DOCTYPE html>
<html lang="en">

<head>
    @include('live.partials.head', ['title' => 'SERC order | ' . $comp->name])
</head>

<body class="h-dvh overflow-hidden flex flex-col gap-6 p-8 bg-gray-50" x-data>
    @include('live.partials.store')

    <div class="flex items-center justify-between gap-6 shrink-0">
        <div class="min-w-0">
            <h1 class="truncate mb-0! xl:text-5xl!">{{ $comp->name }}</h1>
        </div>

        <div class="flex items-center gap-6 shrink-0">
            @include('live.partials.status', ['clockClass' => 'text-6xl', 'pillClass' => 'text-xl'])

            <div class="flex items-center gap-3">
                <div class="bg-white p-1 rounded-md">
                    {!! QrCode::size(96)->generate(route('live', request()->all())) !!}
                </div>
                <p class="text-base font-semibold text-gray-700 w-32">Scan for your team's start time</p>
            </div>
        </div>
    </div>

    <x-live.serc-order :comp="$comp" size="display" class="flex-1" />
</body>

</html>
