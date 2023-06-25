@extends('layout')

@section('title')
Heats and Orders | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events', $comp) }}">Heats and Orders</a>
</div>


@endsection

@section('content')
<div class="">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">SERC Order</h2>
            <a href="{{ route('comps.view.heats', $comp)}}" class="btn">Back</a>
        </div>

        <p>To swap teams, click the first team, it will turn blue. Then click the team you want to swap it with. The page will automatically update and save.</p>

        

        <div class="grid grid-rows-6 gap-3 grid-flow-col" id="all-teams">
            @foreach ($comp->getCompetitionTeams as $team)

              

                <div class="card cursor-pointer hover:bg-bulsca hover:text-white " data-team="{{ $team->id ?? -1}}">
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
                    element.classList.toggle('selected')
                    hasClicked = !hasClicked;
                    return;
                }

                if (element.getAttribute('data-team') === teamFromInput.value) {
                    element.classList.toggle('selected')
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