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
            @endif{{ $serc->getName() }} | {{ $comp->name }} | Results | {{ $brand->name }}
        </title>
    @else
        <title>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif{{ $serc->getName() }} | {{ $comp->name }} | Results | BULSCA
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
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="@if (isset($brand)) {{ $brand->getLogo() }}@else https://www.bulsca.co.uk/storage/logo/blogo.png @endif"
                class="w-32 h-32" alt="">
            <div class="flex flex-col">
                <h2 class="font-bold mb-0">{{ $serc->getName() }}</h2>
                <h4>{{ $comp->name }}</h4>
            </div>
        </div>
        <a class="link"
            href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}"><small>Back</small></a>
        <div class="flex flex-col items-center">



            <h3 class="text-center">Notes for <span class=" whitespace-nowrap">{{ $team->getFullname() }}</span></h3>

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


        <div class=" pb-16 text-center mt-auto">
            <small>
                &copy;
                Noah Hollowell, BULSCA
                2022-{{ date('Y') }}
                @if (isset($brand))
                    <br>Other logos, styles and assets are the property of their respective owners
                    ({{ $brand->name }})
                @endif
            </small>
        </div>
    </div>








    </div>

</body>

</html>
