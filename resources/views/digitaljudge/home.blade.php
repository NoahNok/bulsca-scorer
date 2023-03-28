@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge</h5>
        <br>
        <h2 class="font-bold">{{ $comp->name }}</h2>

        <p>Please Select a SERC</p>

        @foreach ($comp->getSERCs as $serc)
        <div class=" w-[120%] ">
            <p class="p-2 list-none text-lg text-bulsca font-semibold">{{ $serc->getName() }}</p>
            <div class="px-2 flex flex-col space-y-1">
                @foreach ($serc->getJudges as $judge)
                <div class="flex justify-between items-center">
                    <p>{{ $judge->name }}</p>
                    <a href="#" class="link">Start</a>
                </div>
                @endforeach
            </div>
        </div>

        @endforeach
    </div>
</div>
@endsection