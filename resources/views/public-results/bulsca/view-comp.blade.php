<!DOCTYPE html>
<html lang="en">

@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    @if (isset($brand))
        <link rel="icon" type="image/png" href="{{ $brand->getLogo() }}" />
        <title>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif{{ $comp->name }} | Results | {{ $brand->name }}
        </title>
    @else
        <title>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif
            {{ $comp->name }} | Results | BULSCA
        </title>
        <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    @endif



    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden">
    @isset($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endisset
    <div class="w-screen  min-h-screen flex flex-col items-center lg:justify-center space-y-2 my-8  ">
        <img src="@if (isset($brand)) {{ $brand->getLogo() }}@else https://www.bulsca.co.uk/storage/logo/blogo.png @endif"
            class=" w-60 h-60" alt="">
        <h1 class="font-bold">{{ $comp->name }}</h1>
        <a class="link  text-center" href="{{ route('public.results') }}"><small>Back</small></a>


        <hr class="w-96">
        <a href="https://forms.gle/FEc8XJM3SyUma3Br6" target="_blank" rel="noopener noreferrer" class="link">Give
            Feedback</a>
        @if ($comp->areResultsProvisional())
            <div class="p-2 text-center text-lg">
                <p>These results are provisional! <strong>They are subject to change</strong> and should not be
                    considered accurate or final!</p>
            </div>
        @else
            <br>
        @endif



        <div class="flex flex-col space-y-4 w-[80%] lg:w-[65%] xl:w-[50%]">

            <div>

                <div class=" grid grid-cols-3 xl:grid-cols-6  gap-4">
                    @foreach ($comp->getTotalDQs() as $key => $value)
                        <div class="">
                            <h4 class="hmb-0">{{ $value }}</h4>
                            <small class=" capitalize font-semibold">{{ $key }} DQs</small>

                        </div>
                    @endforeach

                    @foreach ($comp->getTotalPens() as $key => $value)
                        <div>
                            <h4 class="hmb-0">{{ $value }}</h4>
                            <small class=" capitalize font-semibold">{{ $key }} Pens</small>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="">

                <h3>SERCs</h3>
                <div class="grid-4">

                    @forelse ($comp->getSERCs->where('viewable', true) as $serc)
                        <a href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}"
                            class="card card-hover">
                            <h4 class="mb-0 text-center">{{ $serc->getName() }}</h4>
                        </a>
                    @empty
                    @endforelse

                </div>
            </div>

            <div class="">
                <h3>Speeds</h3>
                <div class="grid-4">

                    @forelse ($comp->getSpeedEvents->where('viewable', true) as $speed)
                        <a href="{{ route('public.results.speed', [$comp->resultsSlug(), $speed]) }}"
                            class="card card-hover">
                            <h4 class="mb-0 text-center">{{ $speed->getName() }}</h4>
                        </a>
                    @empty
                    @endforelse

                </div>
            </div>
            <br>
            <hr>


            <div class="pt-3">
                <h3>Results</h3>
                <div class="grid-4">

                    @forelse ($comp->getResultSchemas->where('viewable', true) as $schema)
                        <a href="{{ route('public.results.results', [$comp->resultsSlug(), $schema]) }}"
                            class="card card-hover">
                            <h4 class="mb-0 text-center">{{ $schema->name }}</h4>
                        </a>
                    @empty
                    @endforelse

                </div>
            </div>

        </div>



    </div>

</body>

</html>
