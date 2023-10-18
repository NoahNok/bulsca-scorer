@extends('digitaljudge.layout')

@section('content')
    <div class=" w-screen flex flex-col items-center mt-8 space-y-4 px-4 ">

        <div class="flex flex-col space-y-4 items-center w-full ">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
            <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $speed->getName() }}</h5>
            <p class="font-semibold text-bulsca_red md:hidden">Rotate your phone</p>
            <br>
            <h2 class="font-bold text-center w-full break-words">
                Heat {{ $heat }}
            </h2>
            <p>Times must match the format <strong>XX:XX.XXX</strong> exactly!</p>

            <a href="{{ route('dj.speeds.times.index', $speed) }}" class="link">Back</a>


            @php
                $existingLanes = $comp
                    ->getHeatEntries()
                    ->where('heat', $heat)
                    ->orderBy('lane')
                    ->get();
            @endphp

            <form action="" method="post" class="">
                <div class="relative overflow-x-auto w-screen md:w-auto ">
                    <table>

                        <tbody class="divide-y">
                            @for ($lane = 1; $lane <= $comp->getMaxLanes(); $lane++)
                                <tr class=" ">
                                    <td class="p-2 sticky left-0 bg-white ">
                                        {{ $lane }}
                                    </td>


                                    @if ($existingLanes->contains('lane', $lane))
                                        @php
                                            $pLane = $existingLanes->where('lane', $lane)->first();

                                            $sr = App\Models\SpeedResult::where('competition_team', $pLane->team)
                                                ->where('event', $speed->id)
                                                ->first();

                                            $mins = floor($sr->result / 60000);
                                            $secs = ($sr->result - $mins * 60000) / 1000;

                                        @endphp
                                        <td class="p-2 pr-8 border-r whitespace-nowrap sticky left-4 bg-white">

                                            {{ $pLane->getTeam->getFullname() }}

                                        </td>

                                        <td class="">

                                            @if ($speed->getName() == 'Rope Throw')
                                                <input class="p-2 px-4" type="text" placeholder="Ropes In OR 00:00.000"
                                                    name="team-{{ $pLane->team }}-time" id="team-{{ $pLane->team }}-time"
                                                    required x-data x-mask="99:99.999"
                                                    value="{{ $sr->result != null ? ($sr->result > 4 ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : $sr->result) : '' }}}">
                                            @else
                                                <input class="p-2 px-4" type="text" placeholder="00:00.000"
                                                    name="team-{{ $pLane->team }}-time"
                                                    id="team-{{ $pLane->team }}-time" required x-data x-mask="99:99.999"
                                                    value="{{ $sr->result != null ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}}">
                                            @endif


                                        </td>
                                        <td class="">
                                            <input class="p-2" type="text" placeholder="PXXX"
                                                name="team-{{ $pLane->team }}-p" id="team-{{ $pLane->team }}-p"
                                                value="{{ $sr->getPenaltiesAsString() }}">
                                        </td>
                                        <td class="">
                                            <input class="p-2" type="text" placeholder="DQXXX" x-data x-mask="DQ999"
                                                name="team-{{ $pLane->team }}-dq" id="team-{{ $pLane->team }}-dq"
                                                value="{{ $sr->disqualification }}">
                                        </td>
                                    @else
                                        <td class="border-r p-2 pr-8 bg-white"></td>
                                    @endif
                                </tr>
                            @endfor
                        </tbody>

                    </table>
                </div>

                <br>

                @csrf

                <div class="w-full flex justify-center">
                    <button href="#" class="btn">Save & Next</button>
                </div>
            </form>










        </div>
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
        <br>
    </div>
@endsection