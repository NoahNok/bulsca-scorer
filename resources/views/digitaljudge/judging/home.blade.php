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
    <div class="flex flex-col space-y-3  ">

        <div class="space-y-2 w-full">
            @foreach ($judges as $judge)
                <div class="se-card se-card-hover se-card-body">
                    <div class="flex items-center justify-between h-full">
                        <div class="text-left">
                            <h2>{{ $judge->name }}</h3>
                                <p>{{ $judge->getMarkingPoints->count() }} marking points</p>
                        </div>

                        @if ($loop->first != $loop->last)
                            <form action="{{ route('dj.judging.remove-judge.post') }}" method="POST" class="flex">
                                <input type="hidden" name="removeJudgeId" value="{{ $judge->id }}">
                                @csrf
                                <button class="self-center justify-self-center"><svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="size-8 self-center justify-self-center text-red-500 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg></button>
                            </form>
                        @endif




                    </div>
                </div>
            @endforeach

        </div>
        <a href="{{ route('dj.judging.add-judge') }}" class="se-btn w-full!">Add Casualty/Objective</a>

        @if (App\DigitalJudge\DigitalJudge::getTank())
            <p class="font-bold">Marking Tank {{ App\DigitalJudge\DigitalJudge::getTank() }}</p>
        @endif










        <hr class="spacer mb-4!">

        <a href="{{ route('dj.judging.next-team') }}" class="se-btn se-btn-outline-success w-full">Start Judging</a>
        <a href="{{ route('dj.judging.tutorial') }}" class="se-btn se-btn-thin se-btn-primary w-full">Tutorial</a>

        <hr class="spacer mb-3!">







        <h4>Team Order</h4>

        @if ($comp->show_teams_to_judges || $head)
            <ul class=" list-none -mt-2  w-full ">
                @foreach ($serc->getDraw as $draw)
                    @if ($head)
                        <li class=" ">

                            <div class="flex justify-between">

                                <p>{{ $loop->index + 1 }}. {{ $draw->entity->getName() }}</p> <a
                                    href="{{ route('dj.judging.judge-team', [$draw->entity]) }}"
                                    class="link col-start-5">Edit</a>
                            </div>
                        </li>
                    @else
                        <li>{{ $loop->index + 1 }}. {{ $draw->entity->getName() }}</li>
                    @endif
                @endforeach
            </ul>
        @else
            <div>
                <p class="mb-0">There are <strong>{{ $serc->getScorableEntities()->count() }}</strong> SERCs to mark.
                    You
                    <strong>are not</strong> permitted to view team names.
                </p>
            </div>
        @endif


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
