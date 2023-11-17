@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $serc->getName() }}
@endsection
@php
    $backlink = route('dj.home');
    $icon =
        ' <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />';

@endphp

@section('content')
    <div class="flex flex-col space-y-3 items-center ">

        <h2 class="font-bold text-center w-full break-words">
            @forelse ($judges as $judge)
                {{ $judge->name }}
                @if (!$loop->last)
                    {{ '|' }}
                @endif
            @empty
            @endforelse
        </h2>



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



        <a href="{{ route('dj.judging.next-team') }}" class="btn w-full">Start Judging</a>
        <a href="{{ route('dj.judging.tutorial') }}" class="btn btn-thin btn-purple w-full">Tutorial</a>







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

    <script>
        function askToDoTutorial() {

            // See if they have done the tutorial before
            if (localStorage.getItem('serc-tutorial') == 'done') {
                return;
            }
            localStorage.setItem('serc-tutorial', 'done');

            if (confirm('Would you like to go through the SERC marking tutorial?')) {


                window.location.href = "{{ route('dj.judging.tutorial') }}";

            }
        }

        window.onload = askToDoTutorial;
    </script>
@endsection
