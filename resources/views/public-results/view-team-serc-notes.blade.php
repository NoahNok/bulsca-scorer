<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if ($comp->areResultsProvisional())
            (PROVISIONAL)
        @endif{{ $serc->getName() }} | {{ $comp->name }} | Results | BULSCA
    </title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden">
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class="w-32 h-32" alt="">
            <div class="flex flex-col">
                <h2 class="font-bold mb-0">{{ $serc->getName() }}</h2>
                <h4>{{ $comp->name }}</h4>
            </div>
        </div>
        <a class="link"
            href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}"><small>Back</small></a>
        <div class="flex flex-col items-center">



            <h3 class="text-center">Notes for <span class=" whitespace-nowrap">{{ $team->getFullname() }}</span></h3>

            <ul class=" list-disc md:max-w-[40%] ">
                @forelse ($serc->getNotesForTeam($team) as $note)
                    <li>{{ $note->note }} </li>
                @empty
                    <strong>No notes for this team</strong>
                @endforelse
            </ul>








        </div>


    </div>

    <div class=" pb-16">
        <small>
            &copy; BULSCA 2023
        </small>
    </div>





    </div>

</body>

</html>
