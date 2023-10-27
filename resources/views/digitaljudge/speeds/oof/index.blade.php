@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4 px-4 ">

        <div class="flex flex-col space-y-4 items-center w-full ">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $speed->getName() }}</h5>
            <br>
            <h2 class="font-bold text-center w-full break-words">
                Order of Finish
            </h2>


            <a href="{{ route('dj.home') }}" class="link">Back</a>

            <p>Heats will turn green once complete!</p>


            @for ($heat = 1; $heat <= $comp->getMaxHeats(); $heat++)
                @php
                    $missingResult = $comp
                        ->getHeatEntries()
                        ->where('heat', $heat)
                        ->where('oof', '==', 0)
                        ->exists();

                    // Code to check that each team in the heat has an oof result

                @endphp



                <a href="{{ route('dj.speeds.oof.judge', [$speed, $heat]) }}"
                    class="btn {{ $missingResult ? 'btn-primary' : 'btn-success' }}">Heat
                    {{ $heat }}</a>
            @endfor








        </div>
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
        <br>
    </div>
@endsection
