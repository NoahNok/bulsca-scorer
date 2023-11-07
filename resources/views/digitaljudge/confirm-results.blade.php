@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $serc->getName() }}
@endsection

@section('content')
    <div class="flex flex-col space-y-3 ">


        <h2>Confirm Results</h2>


        <a href="{{ route('dj.home') }}" class="link">Back</a>

        <p>Below is a list of each team and their marks for each Judge and Marking Point.</p>



        @foreach ($serc->getTeams() as $team)
            <div class="flex flex-col w-full ">
                <h3>{{ $team->getFullname() }}</h3>

                <div class="flex flex-col pb-8">
                    @foreach ($serc->getJudges as $judge)
                        <h4>{{ $judge->name }}</h4>

                        <div class="border-b pb-4 mb-4">
                            <div class="flex items-center">
                                <h5 class="w-[60%]">Description</h5>
                                <h5 class="ml-auto text-right">Score</h5>
                            </div>
                            <div class="">
                                @foreach ($judge->getMarkingPoints as $mp)
                                    <div
                                        class="flex items-center border-b-2 border-x-2 border-gray-200 first-of-type:border-t-2 ">
                                        <div class="w-[60%] bg-gray-300 p-2">{{ $mp->name }}</div>
                                        <div class="ml-auto text-right pr-6">{{ $mp->getScoreForTeam($team->id) ?: 0 }}
                                        </div>
                                    </div>
                                @endforeach
                                @php
                                    $dq = $serc->getTeamDQ($team);
                                    $penalties = $serc->getTeamPenalties($team);
                                @endphp

                                @if ($penalties)
                                    <div
                                        class="flex items-center border-b-2 border-x-2 bg-gray-300 border-gray-200 first-of-type:border-t-2 ">
                                        <div class="w-[60%] bg-gray-300 ">Penalties</div>
                                        <div class="ml-auto text-right pr-6 w-[40%] bg-white break-words ">
                                            {{ $penalties->codes }}
                                        </div>
                                    </div>
                                @endif

                                @if ($dq)
                                    <div
                                        class="flex items-center border-b-2 border-x-2 border-gray-200 first-of-type:border-t-2 ">
                                        <div class="w-[60%] bg-gray-300 p-2">DQ</div>
                                        <div class="ml-auto text-right pr-6">{{ $dq->code }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach







        <form action="" method="post" class="w-full">
            @csrf
            <button type="submit" class="btn w-full">Confirm</button>
        </form>
    </div>
@endsection
