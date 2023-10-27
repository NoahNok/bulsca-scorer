@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4 px-4 overflow-hidden ">

        <div class="flex flex-col space-y-4 items-center w-full ">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge | {{ $serc->getCompetition->name }} | {{ $serc->getName() }}</h5>
            <br>

            <h2>Confirm Results</h2>


            <a href="{{ route('dj.home') }}" class="link">Back</a>

            <p>Below is a list of each team and their marks for each Judge and Marking Point. If there is a mistake you can
                click the edit button to jump to the respective edit page!</p>



            @foreach ($serc->getTeams() as $team)
                <div class="flex flex-col w-full md:w-auto">
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
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach







            <form action="" method="post">
                @csrf
                <button type="submit" class="btn">Confirm</button>
            </form>
        </div>
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
        <br>
    </div>
@endsection
