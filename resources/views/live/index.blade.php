<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $comp->name }} | Live | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">
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

            <div class="mt-1 mb-2"><a href="{{ route('live.dqs') }}" class="link">DQs & Penalties</a></div>
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
            @if ($comp->getCompetitionTeams->count() == 0)
                <p>No SERC Order available yet!</p>
            @else
                @foreach ($comp->getCompetitionTeams as $team)
                    <div class="card whitespace-nowrap transition-colors " data-team="{{ $team->id }}">
                        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
                        <br>
                        <small class="text-xs font-semibold">Est: <span data-team-time>-</span></small>
                    </div>
                @endforeach
            @endif

        </div>
        <small>Times are an estimate only.</small>
        <br>
        <br>

        <h3>Heats</h3>
        <div class="flex space-x-4 mb-2">
            <p>Finished:</p>


            <div class="px-4 speed speed-1 rounded-md  flex items-center justify-center font-semibold text-sm">
                Rope Throw</div>

            <div class="px-4 speed speed-2 rounded-md  flex items-center justify-center font-semibold text-sm">
                + League Event</div>
            <div class="px-4 speed speed-3 rounded-md  flex items-center justify-center font-semibold text-sm">
                + Swim & Tow</div>
        </div>
        <div class="flex space-x-2  ">
            <div class=" hidden md:block  ">
                <h5>Lane</h5>
                <ol class="space-y-2">
                    @for ($l = 1; $l <= $comp->max_lanes; $l++)
                        <li class="px-5 py-3 border border-transparent">{{ $l }}</li>
                    @endfor
                </ol>
            </div>
            <div class=" w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 3xl:grid-cols-5 gap-3   ">


                @forelse ($comp->getHeatEntries->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
                    <div class="w-full">
                        <h5>Heat {{ $key }}</h5>
                        <ol class=" list-item space-y-2 ">
                            @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                @php
                                    $lane = $heat->where('lane', $l)->first();
                                @endphp


                                <div class="flex flex-row md:block ">
                                    <p class="px-5 py-3 border border-transparent md:hidden">{{ $l }}</p>
                                    <li class="card whitespace-nowrap flex-grow  " data-heat="{{ $key }}">
                                        @if ($lane)
                                            {{ $lane->getTeam->getFullname() }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </li>
                                </div>
                            @endfor
                        </ol>
                    </div>
                @empty

                    <p>No Heats available yet!</p>
                @endforelse

            </div>
        </div>
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
                            minute: '2-digit'
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
                        handleHeatsFinished(data.heatsFinished);
                        switchStatus(true);
                    }).catch(err => {
                        switchStatus(false);
                    });
            }
            setInterval(update, 5000);
            window.onload = update;
        </script>

        <script>
            var elements = [];

            [...document.getElementById('results-scroller').children].forEach(el => {
                elements.push(el)
                el.addEventListener('click', () => {
                    run(true)
                    resetInterval()
                })
                el.addEventListener('dblclick', () => {
                    window.open("{{ route('public.results.comp', $comp->resultsSlug()) }}")
                })
            })

            var paused = false
            var index = 1;

            document.getElementById('results-scroller').addEventListener('mouseenter', () => {
                paused = true;
            });
            document.getElementById('results-scroller').addEventListener('mouseleave', () => {
                paused = false;
            })



            function run(ignorePause = false) {
                if (paused && !ignorePause) {
                    return;
                }

                var element = elements[index]
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'start'
                });
                index++;
                if (index >= elements.length) {
                    index = 0;
                }
            }
            var interv = null;
            interv = setInterval(() => run(), 5000)

            function resetInterval() {
                clearInterval(interv)
                interv = setInterval(() => run(), 5000)
            }
        </script>
</body>

</html>
