@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $serc->getName() }}</h5>
        <br>
        <h2 class="font-bold">{{ $judge->name }}</h2>

        <p class="px-4">There are X teams to Judge. To start judging press "Start Judging"</p>
        <p class="px-4">You will not be able to edit/see scores after submitting them, however you will be able to see you highest, lowest and average score awarded for each criteria at all times.</p>
        <p class="px-4">If you need to get back to the judging page, click "Start Judging" again. It will resume you at the last team you started to judge!</p>

        <a href="{{ route('dj.judging.next-team', $judge) }}" class="btn w-[80%]">Start Judging</a>

        <h4>Team Order</h4>
        <ul class=" list-decimal ">
            @foreach ($comp->getCompetitionTeams as $team)

            @if ($head)
            <li class="">
                <div class="grid grid-cols-5">
                    {{ $team->getFullname() }} <a href="{{ route('dj.judging.judge-team', [$judge, $team]) }}" class="link col-start-5">Edit</a>
                </div>
            </li>
            @else
            <li>{{ $team->getFullname() }}</li>
            @endif


            @endforeach
        </ul>
        <br>


    </div>
    <a href="{{ route('dj.logout') }}" class="link">Logout</a>
</div>
@endsection