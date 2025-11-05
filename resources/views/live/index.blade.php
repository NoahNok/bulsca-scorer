@extends('live.layout')

@section('content')
    <div class="w-[90vw] md:w-[70vw] my-12">
        <div class="w-full flex items-center justify-between">
            <h1 class="mb-0">{{ $comp->name }}</h1>

            <h2 id="time-now"></h2>
        </div>

        <div class="flex justify-between items-center -mt-3">

            <div class="flex items-center space-x-2 ml-2 ">
                <div id="live-status-ping" class="w-2 h-2 rounded-full animate-pulse bg-orange-400"></div>
                <small id="live-status">Waiting...</small>
            </div>

            <div class="mt-1 mb-2"><a href="{{ route('live.dqs', request()->all()) }}" class="link">DQs & Penalties</a>
            </div>
        </div>





        {{-- <h3>Results</h3>
        <small>Double click to see full results</small>
        <div class="block overflow-y-hidden w-full h-[30%] relative" id="results-scroller">

            <div class="w-full h-full p-2 flex space-x-3 ">

                @foreach ($comp->getSERCs as $serc)
                    <div class="grow flex flex-col items-center">
                        <h4>{{ $serc->name }}</h4>
                        <div class=" overflow-hidden">
                            <div class=" flex flex-col space-y-1 " data-autoscroll>
                                @foreach ($serc->getResults() as $result)
                                    <div class="card card-thin card-row w-full ">
                                        {{ $result->place }}.
                                        {{ $result->team }}
                                        <span class="ml-auto"> &nbsp; ({{ round($result->points) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>
                @endforeach


            </div>
            <div class="w-full h-full p-2 flex space-x-3 ">

                @foreach ($comp->getSpeedEvents as $speed)
                    <div class="grow flex flex-col items-center">
                        <h4>{{ $speed->getName() }}</h4>
                        <div class=" overflow-hidden">
                            <div class=" flex flex-col space-y-1 " data-autoscroll>
                                @foreach ($speed->getResults() as $result)
                                    <div class="card card-thin card-row w-full ">
                                        {{ $result->place }}.
                                        {{ $result->team }}
                                        <span class="ml-auto"> &nbsp; ({{ round($result->points) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>
                @endforeach


            </div>


            <div class="w-full h-full p-2 flex space-x-3 ">
                @foreach ($comp->getResultSchemas as $schema)
                    <div class="grow flex flex-col items-center">
                        <h4>{{ $schema->name }}</h4>
                        <div class=" overflow-hidden">
                            <div class=" flex flex-col space-y-1 " data-autoscroll>
                                @foreach ($schema->getDetailedPrint() as $result)
                                    <div class="card card-thin card-row w-full ">
                                        {{ $result->place }}.
                                        {{ $result->team }}
                                        <span class="ml-auto"> &nbsp; ({{ round($result->totalPoints) }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                    </div>
                @endforeach
            </div>



        </div>
        <br> --}}



        <h3>SERC Order</h3>
        <div class="flex space-x-4 mb-2">
            <p>Finished:</p>


            <div class="px-4 finished-1 rounded-md text-white flex items-center justify-center font-semibold text-sm">
                Dry</div>

            <div class="px-4 finished-2 rounded-md text-white flex items-center justify-center font-semibold text-sm">
                Both</div>
        </div>

        <div class="grid grid-rows-12 md:grid-rows-8 2xl:grid-rows-6 gap-3 md:grid-flow-col">
            @php
                $sercDraw = $comp->getDraws();

                $sercDraw = count($sercDraw) > 0 ? $sercDraw[0] : null;

            @endphp
            @if ($comp->getCompetitionTeams->count() == 0 || $sercDraw == null)
                <p>No SERC Order available yet!</p>
            @else
                @foreach ($sercDraw['draws'] as $tank_no => $draws)
                    @foreach ($draws as $draw)
                        <div class="card whitespace-nowrap transition-colors " data-team="{{ $draw->entity->id }}">
                            {{ $draw->draw }}. {{ $draw->entity->getName($comp) }}
                            <br>
                            <small class="text-xs font-semibold">Est: <span data-team-time>-</span></small>
                        </div>
                    @endforeach
                @endforeach
            @endif

        </div>
        <small>Start times are an estimate only.</small>
        <br>
        <br>


        <br>
        <br>

        <script>
            let liveStatus = document.getElementById('live-status');
            let liveStatusPing = document.getElementById('live-status-ping');

            function switchStatus(s) {
                liveStatusPing.classList.remove('bg-orange-400');
                if (!s) {
                    liveStatus.innerText = 'Lost connection, Retrying...';
                    liveStatusPing.classList.remove('bg-green-400');
                    liveStatusPing.classList.add('bg-red-400');
                } else {
                    liveStatus.innerText = 'Live';
                    liveStatusPing.classList.remove('bg-red-400');
                    liveStatusPing.classList.add('bg-green-400');
                }
            }

            function updateTime() {
                let timeNow = new Date();
                document.getElementById('time-now').innerText = timeNow.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                });
            }
            updateTime();
            setInterval(updateTime, 1000);

            function update() {




                function handleSercsFinished(data) {
                    Object.keys(data).forEach(id => {
                        let card = document.querySelector(`[data-team="${id}"]`);
                        card.classList.add('finished', `finished-${data[id]}`);
                    });
                }

                function handleEstimatedTeamTime(avgTeamTime, startTime) {
                    startTime = +startTime

                    function addSeconds(date, seconds) {
                        // Making a copy with the Date() constructor
                        const dateCopy = new Date(date);
                        dateCopy.setTime(dateCopy.getTime() + seconds * 1000);
                        return dateCopy;
                    }

                    let timeNow = new Date();
                    if (startTime) {
                        timeNow = new Date(startTime);

                    }

                    document.querySelectorAll('[data-team]').forEach(card => {

                        if (card.classList.contains('finished')) {
                            let small = card.querySelector('small')
                            small.innerText = card.classList.contains('finished-2') ? 'Finished (Both)' :
                                'Finished (Dry)';



                            return;
                        }

                        let teamTime = card.querySelector('[data-team-time]');

                        teamTime.innerText = timeNow.toLocaleTimeString('en-GB', {
                            hour: '2-digit',
                            minute: '2-digit',
                        });
                        timeNow = addSeconds(timeNow, avgTeamTime);

                    })

                }

                function handleHeatsFinished(data) {
                    Object.keys(data).forEach(heat => {
                        let cards = document.querySelectorAll(`[data-heat="${heat}"]`);

                        cards.forEach(card => {
                            card.classList.add('speed', `speed-${data[heat]}`);
                        });

                    });
                }


                fetch("{{ route('live.data', $comp->id) }}")
                    .then(response => response.json())
                    .then(data => {

                        handleSercsFinished(data.sercsFinished);
                        handleEstimatedTeamTime(data.avgTime, data.sercStartTime);

                        switchStatus(true);
                    }).catch(err => {
                        switchStatus(false);
                    });
            }
            setInterval(update, 5000);
            window.onload = update;
        </script>


    @endsection
