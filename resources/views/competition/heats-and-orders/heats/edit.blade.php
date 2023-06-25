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
            <h2 class="mb-0">Heats</h2>
            <a href="{{ route('comps.view.heats', $comp) }}" class="btn">Back</a>
        </div>

        <p>To swap teams, click the first team, it will turn blue. Then click the team you want to swap it with (including blank spaces). The page will automatically update and save.</p>

        <div class="flex space-x-2  ">
            <div class=" hidden md:block  ">
                <h5>Lane</h5>
                <ol class="space-y-2">
                    @for($l = 1; $l <= $comp->max_lanes; $l++)
                    <li class="px-5 py-3 border border-transparent">{{ $l }}</li>
                    @endfor
                </ol>
            </div>
       
                <div class=" w-full grid grid-cols-1 md:grid-cols-8 gap-3 " id="all-teams" >
      
                
                    @foreach ($heatEntries->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
                        <div >
                            <h5 >Heat {{ $key }}</h5>
                            <ol class=" list-item space-y-2">
                                @for($l = 1; $l <= $comp->max_lanes; $l++)
                                    
                                    @php
                                        $lane = $heat->where('lane', $l)->first()
                                    @endphp
        
                                    <li class="card cursor-pointer hover:bg-bulsca hover:text-white " data-team="{{ $lane->getTeam->id ?? -1}}" data-heat="{{ $key }}" data-lane="{{ $l }}">
                                        @if ($lane)
                                         {{ $lane->getTeam->getFullname() }}
                                        @else
                                          &nbsp;
                                        @endif
                                    </li>
                                 
        
                                   
                                    
                                @endfor
                            </ol>
                        </div>
                        
                    @endforeach
        
                </div>
        </div>

        <br>

        <form action="" method="post" id="team-switch" class="hidden">
            @csrf
            <input type="text" name="team" id="team">
            <input type="text" name="target-lane" id="target-lane">
            <input type="text" name="target-heat" id="target-heat">
        </form>


    </div>
</div>

<script>


    function init() {
        let hasClicked = false;

        let teamToMove = null;

        let teamInput = document.getElementById("team")
        let laneInput = document.getElementById("target-lane")
        let heatInput = document.getElementById("target-heat")
        let form = document.getElementById("team-switch")



        document.getElementById('all-teams').querySelectorAll('[data-team]').forEach(element => {
            
            element.onclick = (event) => {
                if (!hasClicked) {

                    if (element.getAttribute('data-team') === "-1") return

                    teamInput.value = element.getAttribute('data-team');
                    element.classList.toggle('selected')
                    hasClicked = !hasClicked;
                    return;
                }

                if (element.getAttribute('data-team') === teamInput.value) {
                    element.classList.toggle('selected')
                    hasClicked = !hasClicked
                    teamInput.value = ""
                    return
                }


         
                heatInput.value = element.getAttribute('data-heat');
                laneInput.value = element.getAttribute('data-lane');
                
                form.submit()
                


            }

        });
    
    }

    init()



</script>

@endsection