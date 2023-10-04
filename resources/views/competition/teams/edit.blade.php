@extends('layout')

@section('title')
    Edit Teams | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.teams', $comp) }}">Teams</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.teams.edit', $comp) }}">Edit Teams</a>
    </div>
@endsection

@section('content')
    <div class="grid-2" x-data="{
        clubs: [],
        name: '',
        hasChanges: false,
    
    
    
        addClub() {
    
            if (this.clubs.filter((c) => c.name == this.name).length > 0 || this.name == '') return
    
            this.clubs = [...this.clubs, {
                name: this.name,
                teams: [{
                        team: 'A',
                        time: '',
                        league: '1'
                    },
                    {
                        team: 'B',
                        time: '',
                        league: '1'
                    }
                ]
            }]
    
            this.name = ''
        }
    }" @change="hasChanges = true">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">Edit Teams</h2>
                <button table-submit="teams" class="btn">Save</button>
            </div>
            <p>Editable cells are white!
            </p>


            <div class="space-y-4">

                <template x-for="club in clubs" :key="club.name">
                    <div class="card space-y-3" x-data="{
                        addTeam() {
                    
                            if (club.teams.length == 0) {
                                club.teams = [
                                    ...club.teams,
                                    {
                                        team: 'A',
                                        time: '',
                                        league: '1'
                                    }
                                ]
                            } else {
                                club.teams = [
                                    ...club.teams,
                                    {
                                        team: String.fromCharCode(club.teams[club.teams.length - 1].team.charCodeAt(0) + 1),
                                        time: '',
                                        league: '1'
                                    }
                                ]
                            }
                    
                    
                        }
                    }">
                        <div class="flex items-center">
                            <h3 class="mb-0" x-text="club.name"></h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 ml-auto"
                                x-on:click="clubs = clubs.filter((c) => c.name != club.name)">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </div>

                        <div>
                            <div class="grid-4">
                                <label for="" class="">Team</label>
                                <label for="" class="">Time</label>
                                <label for="" class="">League</label>
                            </div>
                            <template x-for="(team, index) in club.teams" :key="team.team">




                                <div class="grid-4 mb-1">

                                    <div class="form-input" style="margin-bottom: 0 !important">

                                        <input class="input" x-model="team.team" style="margin-bottom: 0 !important">
                                    </div>

                                    <div class="form-input" style="margin-bottom: 0 !important">


                                        <input class="input" x-model="team.time" type="time"
                                            style="margin-bottom: 0 !important">
                                    </div>

                                    <div class="form-input" style="margin-bottom: 0 !important">


                                        <select
                                            style="padding-top: 0.65em; padding-bottom: 0.65em; margin-bottom: 0 !important"
                                            x-model="team.league">
                                            <option value="null">Please select an option...</option>
                                            @foreach (App\Models\League::all() as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="flex items-center justify-end"
                                        x-show="index == club.teams.length-1 && index != 0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6"
                                            x-on:click="club.teams = club.teams.filter((t) => t.team != team.team)">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </div>

                                </div>


                            </template>
                        </div>

                        <button class="btn flex items-center justify-center" x-on:click="addTeam()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                        </button>

                    </div>
                </template>

                <div class="card space-y-3">

                    <h3 class="mb-0">Add Club</h3>

                    <div class="form-input">
                        <label for="" class="">Name</label>
                        <input class="input" x-model="name" @keyup.enter="addClub()">
                    </div>

                    <button class="btn" x-on:click="addClub()">Add</button>

                </div>
            </div>


        </div>
        <div class=" row-start-1 md:row-start-auto">
            <div class="alert-box alert-warning">
                <h1>Heat & SERC Order</h1>
                <p>You will need to <strong>regenerate</strong> the Heat and SERC Order after adding any
                    <strong>new</strong> teams.
                    <br>
                    <strong>Tip:</strong> Only generate the heats and SERC Order after adding all your teams!
                </p>
            </div>
            <br>
            <div class="alert-box" x-show="hasChanges">
                <h1>Unsaved Changes</h1>
                <p>You have <strong>unsaved</strong> changes. You need to click the save button to keep your current
                    changes!
                </p>
            </div>
        </div>
    </div>
@endsection
