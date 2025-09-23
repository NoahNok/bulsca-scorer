@extends('layouts.competition')

@section('title')
    Edit Heats | Heats and Draws
@endsection



@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">Heats</h2>
                <a href="{{ route('comps.heats_and_draws', $comp) }}" class="se-btn">Back</a>
            </div>

            <p>To swap <strong>teams</strong>, click the first team, it will turn blue. Then click the team you want to swap
                it with
                (including blank spaces). The page will automatically update and save.
                <br>
                To swap <strong>heats</strong>, click the title of the first heat, the whole heat will turn blue. Then
                select the heat title
                to swap with. The page will automatically update and save.
            </p>


            <div class="se-table" id="all-teams">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                Lane
                            </th>

                            @foreach ($event->heats->sortBy(['heat', 'lane'])->groupBy('heat') as $key => $heat)
                                <th scope="col" data-hn="{{ $key }}">
                                    <div class="flex items-center justify-end whitespace-nowrap">
                                        Heat {{ $key }}
                                        <svg data-delete-heat data-heat="{{ $key }}"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class=" size-4 cross"
                                            serc-builder-marking-point-delete="">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                    </div>
                                </th>
                            @endforeach


                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $tableEntries = $event->heats->sortBy(['heat', 'lane'])->groupBy('heat');
                        @endphp
                        @for ($l = 1; $l <= $comp->max_lanes; $l++)
                            <tr>
                                <th scope="row">
                                    {{ $l }}
                                </th>

                                @foreach ($tableEntries as $key => $heat)
                                    @php
                                        $lane = $heat->where('lane', $l)->first();
                                    @endphp

                                    <td data-heat-id="{{ $lane->id ?? -1 }}" data-heat="{{ $key }}"
                                        data-lane="{{ $l }}"
                                        class="hover:bg-black/60 hover:text-white cursor-pointer min-w-[30ch]">
                                        @if ($lane)
                                            {{ $lane->entity->getName() }}
                                            ({{ $lane->entity->getSeeds()->where('speed_event', $event->id)->first()->prettySeed() }})
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <form action="{{ route('comps.heats_and_draws.heats.swap', [$comp, $event]) }}" method="post" id="team-switch"
                class="hidden">
                @csrf
                <input type="text" name="team" id="team">

                <input type="text" name="target-heat" id="target-heat">

                <input type="text" name="target-heatlane" id="target-hl">



            </form>


        </div>



        <h4>Reset Heats</h4>
        <p>Resetting heats will restore them to their original layout. <strong>You will loose</strong> any alterations you
            have made!</p>
        <br>
        <form action="{{ route('comps.heats_and_draws.heats.reset', [$comp, $event]) }}" method="get"
            onsubmit="return confirm('Are you sure you want to reset the heats?')">
            <button class="se-btn se-btn-danger">Reset</button>
        </form>
    </div>

    <script>
        function init() {
            let hasClicked = false;

            let teamToMove = null;

            let teamInput = document.getElementById("team")

            let heatInput = document.getElementById("target-heat")
            let heatBlankInput = document.getElementById("target-hl")
            let form = document.getElementById("team-switch")



            document.getElementById('all-teams').querySelectorAll('[data-heat-id]').forEach(element => {

                element.onclick = (event) => {
                    if (!hasClicked) {



                        teamInput.value = element.getAttribute('data-heat-id');
                        element.classList.toggle('selected')
                        hasClicked = !hasClicked;
                        return;
                    }

                    if (element.getAttribute('data-heat-id') === teamInput.value) {
                        element.classList.toggle('selected')
                        hasClicked = !hasClicked
                        teamInput.value = ""
                        return
                    }



                    heatInput.value = element.getAttribute('data-heat-id');
                    heatBlankInput.value =
                        `${element.getAttribute('data-heat')}:${element.getAttribute('data-lane')}`


                    form.submit()



                }

            });

            let hasClickedHeat = false
            let firstHeat = "-1"

            document.getElementById('all-teams').querySelectorAll('[data-delete-heat]').forEach(element => {

                element.onclick = (event) => {
                    event.stopPropagation()
                    if (!confirm('Are you sure you want to delete this heat?')) return



                    let fd = new FormData()
                    let heat = element.getAttribute('data-heat')
                    fd.append('heat', heat)
                    fd.append('_token', '{{ csrf_token() }}')
                    @if (request()->has('event'))
                        fd.append('event', '{{ request('event') }}')
                    @endif


                    fetch('#', {
                        method: 'POST',
                        body: fd
                    }).then(res => res.json()).then(data => {
                        if (data.result === "ok") {
                            window.location.reload()
                        }
                    })
                }

            })

            document.getElementById('all-teams').querySelectorAll('[data-hn]').forEach(element => {
                let heat = element.getAttribute('data-hn')
                let lanes = document.getElementById('all-teams').querySelectorAll('[data-heat="' + heat + '"]')
                let title = element

                if (title == null) return

                title.onclick = (event) => {
                    if (!hasClickedHeat) {

                        if (element.getAttribute('data-heat-id') === "-1") return

                        lanes.forEach(l => l.classList.toggle('selected'))
                        firstHeat = heat

                        hasClickedHeat = !hasClickedHeat;
                        return;
                    }

                    if (heat === firstHeat) {
                        lanes.forEach(l => l.classList.toggle('selected'))
                        hasClickedHeat = !hasClickedHeat
                        firstHeat = ""

                        return
                    }



                    let fd = new FormData()
                    fd.append('first', firstHeat)
                    fd.append('second', heat)
                    fd.append('_token', '{{ csrf_token() }}')
                    @if (request()->has('event'))
                        fd.append('event', '{{ request('event') }}')
                    @endif


                    fetch('{{ route('comps.heats_and_draws.heats.swapHeats', [$comp, $event]) }}', {
                        method: 'POST',
                        body: fd
                    }).then(res => res.json()).then(data => {
                        if (data.result === "ok") {
                            window.location.reload()
                        }
                    })






                }

            });






        }

        init()
    </script>
@endsection
