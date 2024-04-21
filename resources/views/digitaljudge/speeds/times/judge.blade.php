@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $speed->getName() }}
@endsection


@php
    $backlink = route('dj.speeds.times.index', $speed);
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
@endphp

@section('content')


    <div class="flex flex-col space-y-3 w-full ">

        <p class="font-semibold text-bulsca_red md:hidden">Rotate your phone</p>

        <h2 class="font-bold  w-full break-words">
            Heat {{ $heat }}
        </h2>
        <p>Times must match the format <strong>XX:XX.XX</strong> exactly! (include leading/trailing 0).<br> Enter <strong>DNF or DNS</strong> as required!
            <br>
            @if ($speed->getName() == 'Rope Throw')
                <strong>OR</strong> enter the total amount of people pulled in from 0-3
            @endif
        </p>


        @php
            $existingLanes = $comp
                ->getHeatEntries()
                ->where('heat', $heat)
                ->orderBy('lane')
                ->get();
        @endphp

        <form action=""
            onsubmit="if (document.getElementById('check-conf').checked == false) { alert('Please check the box to confirm the results are correct'); return false } else  {return true}"
            method="post" class="">
            <div class="relative overflow-x-auto w-full ">
                <table class="w-full">

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
                                            <input class="p-2 px-4" type="text" placeholder="Ropes In OR 00:00.00"
                                                name="team-{{ $pLane->team }}-time" id="team-{{ $pLane->team }}-time"
                                                required x-data
                                                x-mask:dynamic="$input.startsWith('D') ? 'DNa' : '99:99.99'"
                                                value="{{ in_array($sr->disqualification, ['DQ004', 'DQ015']) ? ($sr->disqualification == 'DQ004' ? 'DNS' : 'DNF') : ($sr->result != null ? ($sr->result > 4 ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : $sr->result) : '') }}">
                                        @else
                                            <input class="p-2 px-4" type="text" placeholder="00:00.00"
                                                name="team-{{ $pLane->team }}-time" id="team-{{ $pLane->team }}-time"
                                                required x-data
                                                x-mask:dynamic="$input.startsWith('D') ? 'DNa' : '99:99.99'"
                                                value="{{ in_array($sr->disqualification, ['DQ004', 'DQ015']) ? ($sr->disqualification == 'DQ004' ? 'DNS' : 'DNF') : ($sr->result != null ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '') }}">
                                        @endif


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

            <div class="flex flex-row space-x-2 md:space-x-4 items-center">

                <label for="check-conf">I acknowledge that the above results are correct and cannot be changed, and
                    submission of this form acts as signing it digitally.
                    <br>
                    <small class="text-gray-500">(Clicking the text will also check the box!)</small>
                </label>
                <input type="checkbox" id="check-conf" class="min-w-[20px] min-h-[20px]">
            </div>
            <br>

            <button href="#" class="btn w-full">Save & Next</button>

        </form>










    </div>


@endsection
