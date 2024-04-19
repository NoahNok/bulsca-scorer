
@foreach ($data['leagues'] as $league => $league_data)
    @php

        ksort($league_data);

        $maxPoints = 0;
        $totalPoints = 0;
        $totalEntries = 0;

        $maxPointsTeam = '';
        $maxPointsComp = [];

        $avgPlace = 0;

        foreach ($league_data as $team => $team_data) {
          
            foreach ($team_data as $comp_name => $comp) {

                if ($comp == null) continue;

               

                $totalPoints += $comp['points'];
                $totalEntries++;
                if ($comp['points'] > $maxPoints) {
                    $maxPoints = $comp['points'];
                    $maxPointsTeam = $team;
                    $maxPointsComp = $comp;
                }
                $avgPlace += $comp['place'];
            }
        }

        if ($totalEntries == 0) {
            $totalEntries = 1;
        }

        $avgPoints = $totalPoints / $totalEntries;
        $avgPlace = $avgPlace / $totalEntries;

      

    @endphp




    <div class="card col-span-full lg:col-span-4 3xl:col-span-2 z-30" x-data="{ show: 1 }">

        <div class="flex space-x-2 items-center justify-between">
            <h3>{{ $league }}</h3>
            <div class="flex space-x-2">
                <div class=" transition-colors rounded-full py-1 px-4 text-xs  cursor-pointer hover:bg-bulsca hover:text-white"
                    :class="show == 1 ? 'bg-bulsca text-white' : 'bg-gray-200'" @click="show=1">Graph</div>
                <div class="  transition-colors rounded-full py-1 px-4 text-xs  cursor-pointer hover:bg-bulsca hover:text-white"
                    :class="show == 2 ? 'bg-bulsca text-white' : 'bg-gray-200'" @click="show=2">Table</div>
            </div>
        </div>
        <canvas id="placingChart-{{ $club->name }}-{{ $team ?? ''}}-{{ $league }}" x-league="{{ $league }}" x-show="show==1"
            class="mt-auto mb-auto"></canvas>



        <div class="w-full h-full overflow-x-auto" x-show="show==2" style="display:none">
            <table class="table-auto ">
                <thead class="text-left">
                    <tr>
                        <th class="px-2 whitespace-nowrap ">Team</th>
                        @foreach ($data['competitions'] as $comp)
                            <th class="px-2 whitespace-nowrap ">{{ $comp['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

                    @endphp




                    @foreach ($league_data as $team => $comp_data)
                       
                        <tr>
                            <td class="px-2 ">
                                {{ $team }}
                            </td>
                            @foreach ($data['competitions'] as $comp)
                                <td class="px-2 ">
                                    @php
                                        $team_data = array_key_exists($comp['name'], $comp_data) ? $comp_data[$comp['name']] : null;
                                    @endphp
                                    {{ $team_data ? $nf->format($team_data['place']) : '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach





                </tbody>
            </table>
        </div>


    </div>

    <div class="grid grid-rows-3 col-span-full lg:col-span-2  3xl:col-span-1 gap-y-2 z-20">

        
        <div class="card  min-w-full  relative justify-center ">

            <div class="absolute w-6 h-1 bg-bulsca_red -left-6 hidden lg:block "></div>

            <div class="absolute w-1 h-6 bg-bulsca_red -top-6 lg:hidden "></div>

            <div class="absolute w-1 h-3 bg-bulsca -bottom-3 "></div>


            <div class="flex ">
                <p class="font-semibold  ">Best Score</p>
                <a class="link ml-auto"
                    href="{{ route('public.results.comp', $maxPointsComp['competition_name'] . '.' . $maxPointsComp['competition_id']) }}"><span
                        class="text-sm">{{ $maxPointsComp['competition_name'] }}</span></a>
            </div>
            <h3 class="hmb-0">
                {{ round($maxPoints) }} pts
            </h3>
            <p class=" text-sm flex">

                <span>{{ $club->name }} {{ $maxPointsTeam }} </span>

            </p>
        </div>

        <div class="card  min-w-full  snap-center z-30 relative justify-center ">

            <div class="absolute w-1 h-3 bg-bulsca_red -bottom-3 "></div>

            <div class="flex ">
                <p class="font-semibold  ">Avg Score</p>

            </div>
            <h3 class="hmb-0">
                {{ round($avgPoints) }} pts
            </h3>
            <p class=" text-sm flex">

                <span>Over {{ $totalEntries }} {{ $totalEntries > 1 ? 'entries' : 'entry' }} </span>

            </p>

        </div>
        @php
            $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

        @endphp
        <div class="card  min-w-full  snap-center z-40 justify-center ">

            <div class="flex ">
                <p class="font-semibold  ">Avg Place</p>

            </div>
            <h3 class="hmb-0">
                {{ $nf->format(round($avgPlace)) }}
            </h3>
            <p class=" text-sm flex">

                <span>Over {{ $totalEntries }} {{ $totalEntries > 1 ? 'entries' : 'entry' }} </span>

            </p>

        </div>



    </div>

    <script>
        var ctx = document.getElementById("placingChart-{{ $club->name }}-{{ $team ?? '' }}-{{ $league }}").getContext('2d');




        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @foreach ($data['competitions'] as $comp)
                        '{{ $comp['name'] }}',
                    @endforeach
                ],
                datasets: [

                    @foreach ($league_data as $team => $comp_data)
                        {
                            label: '{{ $team }}',
                            data: [
                                @foreach ($data['competitions'] as $comp)
                                @php
                                    $team_data = array_key_exists($comp['name'], $comp_data) ? $comp_data[$comp['name']] : null;
                                @endphp
                                {{ $team_data ? $team_data['place'] : 'null' }},                            
                            @endforeach
                            ],

                            fill: false,
                            tension: 0.1,
                            spanGaps: true,
                        },
                    @endforeach



                ]
            },
            options: {
                scales: {
                    y: {

                        reverse: true,

                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            callback: function(value, index, values) {
                                return addSuffix(value);
                            },

                        }
                    }
                }
            }
        });
    </script>
@endforeach
