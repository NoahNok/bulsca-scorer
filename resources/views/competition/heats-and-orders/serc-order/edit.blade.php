@extends('layouts.competition')

@section('title')
    Edit SERC Draw | Heats and Draws | {{ $comp->name }}
@endsection



@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">SERC Order</h2>
                <a href="{{ route('comps.heats', $comp) }}" class="se-btn">Back</a>
            </div>

            <p>To swap teams, click the first team, it will turn blue. Then click the team you want to swap it with. The
                page will automatically update and save.</p>



            <div class="grid grid-rows-6 gap-3 md:grid-flow-col" id="all-teams">
                @foreach ($comp->getCompetitionTeams as $team)
                    <div class="se-btn  " style="text-align: left" data-team="{{ $team->id ?? -1 }}">
                        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
                    </div>
                @endforeach
            </div>

            <br>

            <form action="" method="post" id="team-switch" class="hidden">
                @csrf
                <input type="text" name="teamFrom" id="teamFrom">
                <input type="text" name="teamTo" id="teamTo">
            </form>


        </div>
        <h4>Regen SERC Order</h4>
        <p>Regenerating the SERC order will randomly assign teams. <strong>You will loose</strong> any alterations you have
            made!</p>
        <br>
        <form action="{{ route('comps.view.serc-order.regen', $comp) }}" method="get"
            onsubmit="return confirm('Are you sure you want to reset the heats?')">
            <button class="se-btn se-btn-danger">Regen</button>
        </form>
    </div>

    <script>
        function init() {
            let hasClicked = false;

            let teamFromInput = document.getElementById("teamFrom")
            let teamToInput = document.getElementById("teamTo")
            let form = document.getElementById("team-switch")



            document.getElementById('all-teams').querySelectorAll('[data-team]').forEach(element => {

                element.onclick = (event) => {
                    if (!hasClicked) {

                        teamFromInput.value = element.getAttribute('data-team');
                        element.classList.toggle('se-btn-selected')
                        hasClicked = !hasClicked;
                        return;
                    }

                    if (element.getAttribute('data-team') === teamFromInput.value) {
                        element.classList.toggle('se-btn-selected')
                        hasClicked = !hasClicked
                        teamFromInput.value = ""
                        return
                    }


                    teamToInput.value = element.getAttribute('data-team');

                    form.submit()



                }

            });

        }

        init()
    </script>
@endsection
