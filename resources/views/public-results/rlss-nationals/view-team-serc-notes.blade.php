<!DOCTYPE html>
<html lang="en">
@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <title>{{ $comp->name }} | Live | RLSS</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        :root {
            --brand-primary: {{ $brand->primary_color }};
            --brand-secondary: {{ $brand->secondary_color }};
        }
    </style>
</head>

<body class="overflow-x-hidden  w-screen h-screen">


    <div class="w-full h-48 bg-rlss-blue flex flex-col justify-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto">
            <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
            <p class=" font-ariel text-rlss-yellow font-semibold">John Charles Centre for Sport</p>
        </div>


    </div>

    <div class="container mx-auto py-6 overflow-x-hidden">
        <a class="link"
            href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}"><small>Back</small></a>

        <h2 class="font-astoria text-rlss-blue font-extrabold hmb-0">Notes for <span
                class=" whitespace-nowrap">{{ $team->formatName(':N') }}</span></h2>
        <p>{{ $team->formatName(':C (:R) - :L') }}</p>
        <br>



        <div class="md:max-w-[30vw] ">
            @forelse ($serc->getNotesForTeam($team) as $note)
                <p><strong>{{ $note->getJudge->name }}</strong>
                </p>
                <p class="ml-6 mb-3">{{ $note->note }}</p>
            @empty
                <strong>No notes for this team</strong>
            @endforelse
        </div>








    </div>
    <br>


</body>

</html>
