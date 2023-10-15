@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4">

        <div class="flex flex-col space-y-4 items-center mb-6">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge</h5>
            <br>
            <h2 class="font-bold">{{ $comp->name }}</h2>
            @if ($head)
                <h4 class="">(Head Judge/SERC Setter)</h4>
            @endif

            <h3>SERCs</h3>
            <p class="px-4">
                @if ($head)
                    You can manage and alter the following SERC's and Casualties/Objectives below.
                @else
                    Please select which Casualty/Objective you are for the SERC you are judging
                @endif
            </p>

            @foreach ($comp->getSERCs->where('digitalJudgeEnabled') as $serc)
                <div class=" w-[80%] border-2 border-bulsca rounded-md ">
                    <p class="p-2 bg-bulsca text-white list-none text-lg font-semibold flex items-center">
                        {{ $serc->getName() }}
                        @if ($head)
                            @if ($serc->digitalJudgeConfirmed)
                                <span class="btn ml-auto pointer-events-none btn-small">Result Confirmed</span>
                            @else
                                <a href="{{ route('dj.confirm-results', $serc) }}"
                                    class="btn ml-auto btn-white btn-small">Confirm Results</a>
                            @endif
                        @endif
                    </p>
                    <div class="px-3 py-2 flex flex-col space-y-4">
                        @foreach ($serc->getJudges as $judge)
                            <a href="{{ route('dj.judging.confirm-judge', $judge) }}"
                                class="flex justify-between items-center">
                                <p>{{ $judge->name }}</p>
                                <p class=" link">Start</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <h3>Speeds</h3>

            @foreach ($comp->getSpeedEvents as $speed)
                <div class=" w-[80%] border-2 border-bulsca rounded-md ">
                    <p class="p-2 bg-bulsca text-white list-none text-lg font-semibold flex items-center">
                        {{ $speed->getName() }}

                    </p>
                    <div class="px-3 py-2 flex flex-col space-y-4">

                        <a href="{{ route('dj.speeds.times.index', $speed) }}" class="flex justify-between items-center">
                            <p>Times</p>
                            <p class=" link">Start</p>
                        </a>
                        <a href="#" class="flex justify-between items-center hidden">
                            <p>Order of Finish</p>
                            <p class=" link">Start</p>
                        </a>

                    </div>
                </div>
            @endforeach

            <a href="{{ route('dj.logout') }}" class="link">Logout</a>
        </div>
    </div>
@endsection
