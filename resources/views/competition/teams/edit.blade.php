@extends('layouts.competition')

@section('title')
    Edit Teams | {{ $comp->name }}
@endsection





@section('content')
    <div class="" x-data="{
        clubs: {{ $currentTeams }},
        name: '',
        csrf: '{{ csrf_token() }}',
        hasChanges: false,
    
        save() {
            let fd = new FormData()
            fd.append('json', JSON.stringify(this.clubs))
            fd.append('_token', this.csrf)
            fetch('{{ route('comps.view.teams.editPost', $comp) }}', {
                method: 'POST',
                body: fd
            }).then(res => {
                if (res.ok) {
                    showSuccess('Saved teams')
                    setTimeout(() => location.href = `{{ route('comps.teams', $comp) }}`, 500)
                } else {
                    showAlert(`Failed to save teams. Check your inputs and try again!`)
                }
            })
        },
        addClub() {
            if (this.clubs.filter((c) => c.name == this.name).length > 0 || this.name == '') return
    
            this.clubs = [...this.clubs, {
                name: this.name,
                teams: [{
                        team: 'A',
                        time: '00:00',
                        league: '1',
                        id: null
                    },
                    {
                        team: 'B',
                        time: '00:00',
                        league: '1',
                        id: null
                    }
                ]
            }]
    
            this.name = ''
        }
    }" @change="hasChanges = true">


        <div class="alert-box alert-warning">
            <h1>Heat & SERC Order</h1>
            <p>You will need to <strong>regenerate</strong> the Heat and SERC Order after adding any
                <strong>new</strong> teams.
                <br>
                <strong>Tip:</strong> Only generate the heats and SERC Order after adding all your teams!
            </p>
        </div>




        <br>

        <div class="grid-3 row-auto">


            <div class="flex flex-col space-y-4 2xl:col-span-2 order-1 xl:order-none  ">
                <h2 class="">Edit Teams</h2>
                <div class="flex flex-col space-y-4 col-span-2">



                    <div class="space-y-8">

                        <template x-for="club in clubs" :key="club.name">
                            <div class="flex flex-col space-y-4" x-data="{
                                addTeam() {
                            
                                    if (club.teams.length == 0) {
                                        club.teams = [
                                            ...club.teams,
                                            {
                                                team: 'A',
                                                time: '00:00',
                                                league: '1',
                                                id: null
                                            }
                                        ]
                                    } else {
                                        club.teams = [
                                            ...club.teams,
                                            {
                                                team: String.fromCharCode(club.teams[club.teams.length - 1].team.charCodeAt(0) + 1),
                                                time: '00:00',
                                                league: '1',
                                                id: null
                                            }
                                        ]
                                    }
                            
                            
                                }
                            }">
                                <div class="flex items-center space-x-8">

                                    <h2 class="w-full"><input
                                            class="border-b-2 border-b-gray-300 focus:outline-none hover:border-gray-400 focus:border-bulsca font-archivo w-full"
                                            x-model.lazy="club.name"></h2>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="w-6 h-6 ml-auto hover:text-red-600 cursor-pointer"
                                        x-on:click="if (!confirm(`Are you sure you want to remove this club?`)) {return}; clubs = clubs.filter((c) => c.name != club.name); hasChanges = true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>

                                </div>

                                <div class="">
                                    <div class="grid-4 font-archivo">
                                        <label for="" class="">Team</label>
                                        <label for="" class="">Time</label>
                                        <label for="" class="">League</label>
                                    </div>
                                    <template x-for="(team, index) in club.teams" :key="team.team">




                                        <div class="flex items-center space-x-8">
                                            <div class="grid grid-cols-3 gap-4 mb-1 font-archivo w-full">

                                                <div class="se-form-input" style="margin-bottom: 0 !important">

                                                    <input class="input" x-model="team.team"
                                                        style="margin-bottom: 0 !important">
                                                </div>

                                                <div class="se-form-input" style="margin-bottom: 0 !important">


                                                    <input class="input" x-model="team.time" type="time"
                                                        style="margin-bottom: 0 !important">
                                                </div>

                                                <div class="se-form-input" style="margin-bottom: 0 !important">


                                                    <select style="margin-bottom: 0 !important" x-model="team.league">
                                                        <option value="null">Please select an option...</option>
                                                        @foreach (App\Models\League::where('scoring_type', 'bulsca')->get() as $option)
                                                            <option value="{{ $option->id }}">
                                                                {{ $option->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>




                                            </div>

                                            <div class="flex items-center justify-end max-w-10">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="w-6 h-6 hover:text-red-600 cursor-pointer"
                                                    x-on:click="if (!confirm(`Are you sure you want to remove this team?`)) {return}; club.teams = club.teams.filter((t) => t.team != team.team); hasChanges = true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </div>
                                        </div>


                                    </template>
                                </div>

                                <button class="se-btn se-btn-outline-success flex items-center justify-center"
                                    x-on:click="addTeam()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>

                                </button>

                                <br>

                                <hr class="spacer">

                            </div>
                        </template>

                        <div class="flex flex-col space-y-3 font-archivo">

                            <h3 class="mb-0">Add Club</h3>

                            <div class="se-form-input ">

                                <input class="input" placeholder="Club name" x-model="name" @keyup.enter="addClub()">
                            </div>

                            <button class="se-btn se-btn-outline-success -mt-3" x-on:click="addClub()">Add</button>

                        </div>
                    </div>


                </div>
            </div>



            <div class="  h-full grow ">
                <div class="flex flex-col space-y-4 sticky top-4">
                    <button @click="save()" class="se-btn se-btn-success  md:ml-auto ">Save</button>
                    <div class="alert-box 0" x-show="hasChanges" style="display: none">
                        <h1>Unsaved Changes</h1>
                        <p>You have <strong>unsaved</strong> changes. You need to click the save button to keep your current
                            changes!
                        </p>
                    </div>


                </div>
            </div>

        </div>





    </div>
@endsection
