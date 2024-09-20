<!DOCTYPE html>
<html lang="en">
@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ $brand->getLogo() }}" />
    <title>{{ $comp->name }} | Results | RLSS</title>


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


    <div class="w-full h-48 bg-rlss-blue flex  items-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto flex flex-row items-center">
            <div>
                <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
                <p class=" font-ariel text-rlss-yellow font-semibold">{{ $comp->where }}</p>
            </div>

            <div class="!ml-auto   ">
                <img src="{{ $brand->getLogo() }}" class=" w-20 h-20" alt="">
            </div>
        </div>



    </div>

    <div class="container mx-auto py-6 overflow-x-hidden">


        <h2 class="font-astoria text-rlss-blue font-extrabold">Overall</h2>
        <div class="grid-5">

            @forelse ($comp->getResultSchemas->where('viewable', true)->chunk(2) as $chunk)

                <div class="flex flex-col space-y-3 mb-6">
                    @foreach ($chunk as $schema)
                        <a href="{{ route('public.results.results', [$comp->resultsSlug(), $schema]) }}"
                            class="card card-hover">
                            <h4 class="mb-0 text-center">{{ $schema->name }}</h4>
                        </a>
                    @endforeach
                </div>


            @empty
            @endforelse

        </div>

        <br>

        <h2 class="font-astoria text-rlss-blue font-extrabold">Events</h2>
        <div class="grid-4 text-rlss-blue">

            @forelse ($comp->getSpeedEvents as $speed)
                <a href="{{ route('public.results.speed', [$comp->resultsSlug(), $speed]) }}" class="card card-hover">
                    <h4 class="mb-0 text-center">{{ $speed->getName() }}</h4>
                </a>
            @empty
            @endforelse

            @forelse ($comp->getSERCs as $serc)
                <a href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}"
                    class="card card-hover card-row space-x-3 justify-center items-center">
                    <h4 class="mb-0 text-center">{{ $serc->getName() }}</h4>


                </a>
            @empty
            @endforelse

        </div>







    </div>
    <br>


</body>

</html>
