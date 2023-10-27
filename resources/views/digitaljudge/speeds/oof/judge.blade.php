@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4 px-4 ">

        <div class="flex flex-col space-y-4 items-center w-full ">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $speed->getName() }}</h5>

            <br>
            <h2 class="font-bold text-center w-full break-words">
                Heat {{ $heat }}
            </h2>


            <a href="{{ route('dj.speeds.times.index', $speed) }}" class="link">Back</a>

            @php
                $existingLanes = $comp
                    ->getHeatEntries()
                    ->where('heat', $heat)
                    ->orderBy('lane')
                    ->get();
            @endphp

            <div class="flex flex-col space-y-3">
                @for ($lane = 1; $lane <= $comp->getMaxLanes(); $lane++)
                    @if ($existingLanes->contains('lane', $lane))
                        @php
                            $pLane = $existingLanes->where('lane', $lane)->first();

                            $sr = App\Models\SpeedResult::where('competition_team', $pLane->team)
                                ->where('event', $speed->id)
                                ->first();

                            $mins = floor($sr->result / 60000);
                            $secs = ($sr->result - $mins * 60000) / 1000;

                        @endphp


                        <button class="btn btn-primary ">
                            Lane {{ $lane }}: {{ $pLane->getTeam->getFullname() }}
                        </button>
                    @else
                        <button class="btn btn-white " style="cursor: not-allowed">
                            Lane {{ $lane }}: Empty
                        </button>
                    @endif
                @endfor
            </div>

            <br>

            <div class="w-full flex justify-center">
                <button href="#" class="btn">Save & Next</button>
            </div>











        </div>
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
        <br>
    </div>
@endsection
