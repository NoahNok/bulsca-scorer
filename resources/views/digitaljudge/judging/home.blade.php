@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4 px-4 ">

        <div class="flex flex-col space-y-4 items-center w-full ">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $serc->getName() }}</h5>
            <br>
            <h2 class="font-bold text-center w-full break-words">
                @forelse ($judges as $judge)
                    {{ $judge->name }}
                    @if (!$loop->last)
                        {{ '|' }}
                    @endif
                @empty
                @endforelse
            </h2>

            <a href="{{ route('dj.home') }}" class="link">Back</a>

            <p class="px-4">There are {{ $comp->getCompetitionTeams->count() }} teams. If you need to officiate multiple
                casualties/objectives then click "Add Casualty/Objective" below. To start judging press "Start Judging"</p>
            <p class="px-4">You will not be able to edit/see scores after submitting them, however you will be able to see
                the highest, lowest and average score awarded for each criteria at all times.</p>
            <p class="px-4">If you need to get back to the judging page, click "Start Judging" again. It will resume at
                the last team you started to judge!</p>



            <a href="{{ route('dj.judging.add-judge') }}" class="link">Add Casualty/Objective</a>

            @if (count($judges) > 1)
                <a href="{{ route('dj.judging.remove-judge') }}" class="link">Remove Casualty/Objective</a>
            @endif

            <a href="{{ route('dj.judging.next-team') }}" class="btn w-[80%]">Start Judging</a>







            <h4>Team Order</h4>
            <ul class=" list-decimal ">
                @foreach ($comp->getCompetitionTeams as $team)
                    @if ($head)
                        <li class="">
                            <div class="grid grid-cols-2">
                                <p>{{ $team->getFullname() }}</p> <a href="{{ route('dj.judging.judge-team', [$team]) }}"
                                    class="link col-start-5">Edit</a>
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
        <br>
    </div>
@endsection
