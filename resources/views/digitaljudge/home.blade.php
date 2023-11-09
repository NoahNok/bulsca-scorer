@extends('digitaljudge.mpa-layout')
@section('title')
    Judge
@endsection
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />

';
@endphp
@section('content')

    <div class="flex flex-col space-y-3">


        <h4>SERCs</h4>
        <p>
            @if ($head)
                You can manage and alter the following SERC's and Casualties/Objectives below.
            @else
                Please select which Casualty/Objective you are for the SERC you are judging
            @endif
        </p>

        @foreach ($comp->getSERCs->where('digitalJudgeEnabled') as $serc)
            <div class="  border-2 border-bulsca rounded-md ">
                <p class="p-2 bg-bulsca text-white list-none text-lg font-semibold flex items-center">
                    {{ $serc->getName() }}
                    @if ($head)
                        @if ($serc->digitalJudgeConfirmed)
                            <span class="btn ml-auto pointer-events-none btn-small">Result Confirmed</span>
                        @else
                            <a href="{{ route('dj.confirm-results', $serc) }}" class="btn ml-auto btn-white btn-small">Confirm
                                Results</a>
                        @endif
                    @endif
                </p>
                <div class="px-3 py-2 flex flex-col space-y-4">
                    @foreach ($serc->getJudges as $judge)
                        <a href="{{ route('dj.judging.confirm-judge', $judge) }}" class="flex justify-between items-center">
                            <p>{{ $judge->name }}</p>
                            <p class=" link">Start</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        <br>

        <h4>Speeds</h4>

        @if ($comp->getMaxHeats() == -1)
            <div class="alert-box">
                <p class="font-semibold">No Heats Set</p>
                <p class="text-sm">You have not generated heats yet. You will not be able to digitally judge any speeds
                    events until you do so!</p>
            </div>
        @else
            @foreach ($comp->getSpeedEvents->where('digitalJudgeEnabled') as $speed)
                <div class="  border-2 border-bulsca rounded-md ">
                    <p class="p-2 bg-bulsca text-white list-none text-lg font-semibold flex items-center">
                        {{ $speed->getName() }}
                        @if ($head)
                            @if ($speed->digitalJudgeConfirmed)
                                <span class="btn ml-auto pointer-events-none btn-small">Result Confirmed</span>
                            @else
                                <a href="{{ route('dj.confirm-results.speed', $speed) }}"
                                    class="btn ml-auto btn-white btn-small">Confirm
                                    Results</a>
                            @endif
                        @endif
                    </p>
                    <div class="px-3 py-2 flex flex-col space-y-4">

                        <a href="{{ route('dj.speeds.times.index', $speed) }}" class="flex justify-between items-center">
                            <p>Times</p>
                            <p class=" link">Start</p>
                        </a>

                        <a href="{{ route('dj.speeds.oof.index', $speed) }}" class="flex justify-between items-center">
                            <p>Order of Finish</p>
                            <p class=" link">Start</p>
                        </a>


                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="text-center mt-6">
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
    </div>






@endsection
