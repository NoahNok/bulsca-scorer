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
        <h1>{{ $comp->name }}</h1>
        <br>

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
                    <div class="card whitespace-nowrap transition-colors" data-team="{{ $team->id }}">
                        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
                        <br>
                        <small class="text-xs font-semibold">Est: <span data-team-time>-</span></small>
                    </div>
                @endforeach
            @endif

        </div>
        <small>Times are an estimate only based on the average time between each team.</small>
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
            function update() {


                function handleSercsFinished(data) {
                    Object.keys(data).forEach(id => {
                        let card = document.querySelector(`[data-team="${id}"]`);
                        card.classList.add('finished', `finished-${data[id]}`);
                    });
                }

                function handleEstimatedTeamTime(avgTeamTime, startTime) {
                    console.log(avgTeamTime)

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
                            timeNow = addSeconds(timeNow, avgTeamTime);
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
                    });
            }
            setInterval(update, 5000);
            window.onload = update;
        </script>
</body>

</html>
