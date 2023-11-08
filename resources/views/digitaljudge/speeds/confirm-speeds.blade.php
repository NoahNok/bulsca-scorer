@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $speed->getName() }}
@endsection

@php
    $backlink = route('dj.home');
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />';
@endphp

@section('content')
    <div class="flex flex-col space-y-3 ">
        <p class="font-semibold text-bulsca_red md:hidden">Rotate your phone</p>

        <h2>Confirm Results</h2>



        <a href="{{ route('dj.home') }}" class="link">Back</a>

        <p>Each heat displays the Lane number, Order of Finish, Result, Team name, DQ and Penalties.


        </p>




        @foreach ($comp->getHeatEntries->sortBy(['heat', 'lane'])->groupBy('heat') as $key => $heat)
            <div>
                <h5>Heat {{ $key }}</h5>
                <ol class=" list-item space-y-2">
                    <li class="flex space-x-3 w-full">
                        <span>L</span>
                        <span>OOF</span>
                        <span class="px-4 pr-16">Result</span>
                        <div class="grid grid-cols-3 flex-grow md:px-4">
                            <span>Team</span>
                            <span>DQ</span>
                            <span>Penalties</span>
                        </div>
                    </li>
                    @for ($l = 1; $l <= $comp->max_lanes; $l++)
                        @php
                            $lane = $heat->where('lane', $l)->first();
                        @endphp

                        <li class="flex space-x-2 w-full ">
                            <div class="flex items-center justify-center">
                                {{ $l }}
                            </div>
                            <div class="card justify-center">
                                {{ $lane?->getOOF($speed->id)->oof ?? '-' }}
                            </div>


                            @if ($lane)
                                @php
                                    $sr = \App\Models\SpeedResult::where('event', $speed->id)
                                        ->where('competition_team', $lane->team)
                                        ->first();
                                @endphp
                                <div class="card justify-center">
                                    {{ $sr->getResultAsString() ?? '-' }}
                                </div>
                                <div class="card flex-grow ">
                                    <div class="grid-3">
                                        <div>{{ $lane->getTeam->getFullname() }}</div>
                                        <div>{{ $sr->disqualification }}</div>
                                        <div>{{ $sr->getPenaltiesAsString() }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="card flex-grow " style="background-color: rgba(0,0,0,0.1)"></div>
                            @endif


                        </li>
                    @endfor
                </ol>
            </div>
        @endforeach


        <br>



        <form action="" method="post" class="w-full">
            @csrf
            <button type="submit" class="btn w-full">Confirm</button>
        </form>
    </div>
@endsection
