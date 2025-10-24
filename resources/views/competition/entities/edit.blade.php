@extends('layouts.competition')

@section('title')
    Entries & Leagues
@endsection


@section('content')
    <h2>Edit Entries</h2>

    <div x-data="{
        data: {{ json_encode($comp->getEntityMakeup()) }},
    
        league_map: {{ $comp->getLeagues->pluck('name', 'id') }},
    
        speed_events: {{ json_encode($comp->getSeedableEvents()) }},
    
        seed_settings: {
            use_seeds: {{ $comp->use_seeds ? 'true' : 'false' }},
            seed_per_event: {{ $comp->seed_per_event ? 'true' : 'false' }},
        },
    
        has_changes: false,
    
        add: {
            club: {
                name: ''
            },
            team: {
                name: '',
                club: null,
                league: null,
                seeds: {}
            },
            competitor: {
                name: '',
                club: null,
                team: null,
                league: null,
                seeds: {}
            }
        },
    
        edit: {
            club: {
                name: ''
            },
            team: {
                name: '',
                league: null,
                seeds: {}
            },
            competitor: {
                name: '',
                league: null,
                seeds: {}
            }
        },
    
        removed: {
            clubs: [],
            teams: [],
            competitors: []
        },
    
    
        addClub() {
            this.modals.addClub = true
        },
    
        createClub() {
            if (!this.$refs.addClub.checkValidity()) {
                this.$refs.addClub.reportValidity()
                return
            }
            let name = this.add.club.name.trim()
    
            if (name == '') {
                showAlert('Please enter a club name')
                return
            }
    
            this.add.club.name = ''
    
            this.data.clubs.push({
                id: null,
                name: name,
                teams: []
            })
    
            this.modals.addClub = false
        },
    
        editClub(club_index) {
            this.edit.club = this.data.clubs[club_index]
            this.modals.editClub = true
        },
    
        removeClub(club_index) {
            let club = this.data.clubs[club_index]
            if (!confirm(`Are you want to remove '${club.name}'? This change will not be permanent until you click save!`)) {
                return
            }
    
            this.data.clubs.splice(club_index, 1)
    
            if (club.id == null) {
                return
            }
    
            this.removed.clubs.push(club.id)
        },
    
        addTeam(club = null) {
            this.add.team.club = club
            this.modals.addTeam = true
        },
    
        editTeam(team_index, club_index = null) {
    
            let team = null
    
            if (club_index != null) {
                team = this.data.clubs[club_index].teams[team_index]
            } else {
                team = this.data.teams[team_index]
            }
    
            this.speed_events.forEach(event => {
                if (team.seeds[event.id]) {
                    return
                }
    
                team.seeds[event.id] = { seed: '', id: null }
            })
    
            this.edit.team = team
    
    
            this.modals.editTeam = true
        },
    
        createTeam() {
            if (!this.$refs.addTeam.checkValidity()) {
                this.$refs.addTeam.reportValidity()
                return
            }
    
            let name = this.add.team.name.trim()
    
            if (name == '') {
                showAlert('Please enter a team name')
                return
            }
    
            if (!this.checkSeedTimes(this.add.team.seeds)) {
                showAlert('Invalid seed time')
                return
            }
    
            let data = {
                id: null,
                name: name,
                league: this.add.team.league,
                competitors: [],
                seeds: JSON.parse(JSON.stringify(this.add.team.seeds))
            }
            if (this.add.team.club == null) {
                this.data.teams.push(data)
            } else {
                this.data.clubs[this.add.team.club].teams.push(data)
            }
    
    
    
            this.add.team.name = ''
    
    
            this.speed_events.forEach(event => {
                this.add.team.seeds[event.id] = { seed: '', id: null }
            })
    
    
            this.modals.addTeam = false
        },
    
        removeTeam(team_index, club_index = null) {
    
            let team = null;
            if (club_index != null) {
                team = this.data.clubs[club_index].teams[team_index]
            } else {
                team = this.data.teams[team_index]
            }
    
            if (!confirm(`Are you want to remove '${team.name}'? This change will not be permanent until you click save!`)) {
                return
            }
    
            if (club_index != null) {
                this.data.clubs[club_index].teams.splice(team_index, 1)
            } else {
                this.data.teams.splice(team_index, 1)
            }
    
            if (team.id == null) {
                return
            }
    
            this.removed.teams.push(team.id)
        },
    
        addCompetitor(club = null, team = null) {
            this.add.competitor.club = club
            this.add.competitor.team = team
            this.modals.addCompetitor = true
        },
    
        editCompetitor(competitor_index, team_index = null, club_index = null) {
    
            let competitor = null
    
            if (team_index != null && club_index != null) {
                competitor = this.data.clubs[club_index].teams[team_index].competitors[competitor_index]
            } else if (team_index != null && club_index == null) {
                competitor = this.data.teams[team_index].competitors[competitor_index]
            } else {
                competitor = this.data.competitors[competitor_index]
            }
    
            console.log(competitor)
    
            this.speed_events.forEach(event => {
                if (competitor.seeds[event.id]) {
                    return
                }
    
                competitor.seeds[event.id] = { seed: '', id: null }
            })
    
    
    
            this.edit.competitor = competitor
    
    
            this.modals.editCompetitor = true
        },
    
        createCompetitor() {
            if (!this.$refs.addCompetitor.checkValidity()) {
                this.$refs.addCompetitor.reportValidity()
                return
            }
    
            let name = this.add.competitor.name.trim()
    
            if (name == '') {
                showAlert('Please enter a competitor name')
                return
            }
    
            console.log(this.add.competitor.club)
            if (this.add.competitor.club != null && this.add.competitor.club != 'null' && this.add.competitor.team == null) {
                showAlert('Please select a team')
                return
            }
    
            if (!this.checkSeedTimes(this.add.competitor.seeds)) {
                showAlert('Invalid seed time')
                return
            }
    
            let data = {
                id: null,
                name: name,
                league: this.add.competitor.league,
                seeds: this.add.competitor.seeds
            }
    
            if (this.add.competitor.club != null && this.add.competitor.team != null) {
                this.data.clubs[this.add.competitor.club].teams[this.add.competitor.team].competitors.push(data)
            } else if (this.add.competitor.club == null && this.add.competitor.team != null) {
                this.data.teams[this.add.competitor.team].competitors.push(data)
            } else {
                this.data.competitors.push(data)
            }
    
    
    
            this.add.competitor.name = ''
            this.speed_events.forEach(event => {
                this.add.competitor.seeds[event.id] = { seed: '', id: null }
            })
    
    
            this.modals.addCompetitor = false
        },
    
        removeCompetitor(competitor_index, team_index = null, club_index = null) {
            let competitor = null
            if (team_index != null && club_index != null) {
                competitor = this.data.clubs[club_index].teams[team_index].competitors[competitor_index]
            } else if (team_index != null && club_index == null) {
                competitor = this.data.teams[team_index].competitors[competitor_index]
            } else {
                competitor = this.data.competitors[competitor_index]
            }
    
    
            if (!confirm(`Are you want to remove '${competitor.name}'? This change will not be permanent until you click save!`)) {
                return
            }
    
            if (team_index != null && club_index != null) {
                this.data.clubs[club_index].teams[team_index].competitors.splice(competitor_index, 1)
            } else if (team_index != null && club_index == null) {
                this.data.teams[team_index].competitors.splice(competitor_index, 1)
            } else {
                this.data.competitors.splice(competitor_index, 1)
            }
    
            if (competitor.id == null) {
                return
            }
    
            this.removed.competitors.push(competitor.id)
    
    
        },
    
    
        seed_regex: /^\d{2}:\d{2}\.\d{3}$/,
        checkSeedTimes(seeds) {
            let all_good = true
            for ([id, seed_item] of Object.entries(seeds)) {
                time = seed_item.seed
                let valid_seed = time === '' || this.seed_regex.test(time)
    
                if (valid_seed) {
                    continue
                }
    
                seed_item.error = true
                all_good = false
            }
    
            return all_good;
        },
    
        checkSeedTime(seed) {
    
    
            return !(seed.seed == '' || this.seed_regex.test(seed.seed))
    
    
        },
    
        async save() {
    
            this.has_changes = false
    
            let response = await fetch('{{ route('comps.entities.save', $comp) }}', {
                method: 'POST',
                body: JSON.stringify({
                    data: this.data,
                    removed: this.removed
                }),
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
            })
    
            if (!response.ok) {
                showAlert('Failed to save, somethign went wrong.')
                return
            }
    
            window.location.href = '{{ route('comps.entities', $comp) }}'
    
        },
    
    
    
        init() {
            if (!('teams' in this.data)) {
                this.data.teams = []
            }
    
            if (!('competitors' in this.data)) {
                this.data.competitors = []
            }
    
            $watch('data', value => this.has_changes = true)
    
            this.speed_events.forEach(event => {
                this.edit.team.seeds[event.id] = { seed: '', id: null }
                this.add.team.seeds[event.id] = { seed: '', id: null }
                this.edit.competitor.seeds[event.id] = { seed: '', id: null }
                this.add.competitor.seeds[event.id] = { seed: '', id: null }
            })
    
            window.addEventListener('beforeunload', (event) => {
                if (this.has_changes) {
                    event.preventDefault();
                    event.returnValue = '';
                }
            });
    
        }
    
    }">


        <div class="grid-4 gap-10!">
            <div class="grid-2 col-span-3 gap-10!">
                <template x-for="(club, cl_index) in data.clubs" :key="cl_index">
                    <div class="">
                        <div class="flex items-center justify-between">
                            <h2 x-text="club.name" class="hover:scale-101 cursor-pointer hover:underline"
                                @click="editClub(cl_index)"></h2>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="size-6 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                @click="removeClub(cl_index)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>

                        </div>

                        <div class="ml-6 space-y-4">
                            <template x-for="(team, t_index) in club.teams" :key="t_index">
                                <div>

                                    <div class="flex items-center justify-between">
                                        <h4 x-text="team.name" class="hover:scale-101 cursor-pointer hover:underline"
                                            @click="editTeam(t_index, cl_index)"></h4>


                                        <div class="flex items-center space-x-2">
                                            <p x-text="league_map[team.league]"></p>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor"
                                                class="size-5 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                                @click="removeTeam(t_index, cl_index)">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </div>

                                    </div>

                                    <div class="ml-6">
                                        <template x-for="(competitor, c_index) in team.competitors" :key="c_index">
                                            <div class="flex items-center justify-between">
                                                <h5 x-text="competitor.name"
                                                    class="hover:scale-101 cursor-pointer hover:underline"
                                                    @click="editCompetitor(c_index, t_index, cl_index)"></h5>
                                                <div class="flex items-center space-x-2">
                                                    <p x-text="league_map[competitor.league]"></p>

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        class="size-4 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                                        @click="removeCompetitor(c_index, t_index, cl_index)">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </template>

                                        <button
                                            class="se-btn se-btn-outline-success w-full mt-2 flex items-center justify-center"
                                            style="" @click="addCompetitor(cl_index, t_index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg> <small class="!mt-[0.15rem] ml-1">Competitor</small>
                                        </button>
                                    </div>

                                </div>
                            </template>


                            <button class="se-btn se-btn-outline-success w-full mt-2 flex items-center justify-center"
                                style="" @click="addTeam(cl_index)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg> <small class="!mt-[0.15rem] ml-1">Team</small>
                            </button>
                        </div>


                    </div>
                </template>

                <template x-for="(team, t_index) in data.teams" :key="t_index">
                    <div>

                        <div class="flex items-center justify-between">
                            <h4 x-text="team.name" class="hover:scale-101 cursor-pointer hover:underline"
                                @click="editTeam(t_index)"></h4>

                            <div class="flex items-center space-x-2">
                                <p x-text="league_map[team.league]"></p>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor"
                                    class="size-5 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                    @click="removeTeam(t_index)">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </div>

                        </div>

                        <div class="ml-6">
                            <template x-for="(competitor, c_index) in team.competitors" :key="c_index">
                                <div class="flex items-center justify-between">
                                    <h5 x-text="competitor.name" class="hover:scale-101 cursor-pointer hover:underline"
                                        @click="editCompetitor(c_index, t_index)"></h5>
                                    <div class="flex items-center space-x-2">
                                        <p x-text="league_map[competitor.league]"></p>

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor"
                                            class="size-4 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                            @click="removeCompetitor(c_index, t_index)">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                            </template>

                            <button class="se-btn se-btn-outline-success w-full mt-2 flex items-center justify-center"
                                style="" @click="addCompetitor(null, t_index)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg> <small class="!mt-[0.15rem] ml-1">Competitor</small>
                            </button>
                        </div>

                    </div>
                </template>

                <template x-for="(competitor, c_index) in data.competitors" :key="c_index">
                    <div class="flex items-center justify-between">
                        <h5 x-text="competitor.name" class="hover:scale-101 cursor-pointer hover:underline"
                            @click="editCompetitor(c_index)"></h5>
                        <div class="flex items-center space-x-2">
                            <p x-text="league_map[competitor.league]"></p>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor"
                                class="size-4 hover:text-red-500 transition-all hover:rotate-90 cursor-pointer"
                                @click="removeCompetitor(c_index)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex flex-col">
                <div class="sticky top-4 flex flex-col space-y-4">
                    <button class="se-btn se-btn-outline-success" x-on:click="addClub()">Add Club</button>
                    <button class="se-btn se-btn-outline-success" x-on:click="addTeam()">Add Team</button>
                    <button class="se-btn se-btn-outline-success" x-on:click="addCompetitor()">Add Competitor</button>
                    <hr class="spacer">
                    <button class="se-btn se-btn-success" @click="save()">Save</button>
                    <div class="alert-box 0" x-show="has_changes" style="display: none">
                        <h1>Unsaved Changes</h1>
                        <p>You have <strong>unsaved</strong> changes. You need to click the save button to keep your current
                            changes!
                        </p>
                    </div>

                    <div class="alert-box alert-warning mb-3"
                        x-show="seed_settings.use_seeds && speed_events.length == 0">
                        <h1>Seeds</h1>
                        <p>You will be unable to add seed times until you have added a speed event!</p>
                    </div>

                </div>

            </div>




        </div>

        <x-s-e-modal id="addClub" title="Add Club">
            <form x-ref="addClub">
                <div class="se-form-input">
                    <input type="text" x-model="add.club.name">
                </div>
            </form>

            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-outline-success" @click="createClub()">Add</button>
            </x-slot>
        </x-s-e-modal>

        <x-s-e-modal id="editClub" title="Edit Club">

            <p>Edits are applied instantly</p>
            <br>

            <div class="se-form-input">
                <input type="text" x-model="edit.club.name">
            </div>


        </x-s-e-modal>


        <x-s-e-modal id="addTeam" title="Add Team">

            <form x-ref="addTeam">
                <div class="grid-4 ">
                    <div class="se-form-input col-span-2">
                        <input type="text" x-model="add.team.name" placeholder="Name" required>
                    </div>

                    <div class="se-form-input">
                        <select name="" x-model="add.team.league" id="">
                            <option value="null">No league</option>
                            <template x-for="[key, value] in Object.entries(league_map)" :key="key">
                                <option :value="key" x-text="value"></option>

                            </template>
                        </select>
                    </div>
                    <div class="se-form-input">
                        <select name="" id="" x-model="add.team.club">
                            <option value="null">No club</option>
                            <template x-for="(club, index) in data.clubs" :key="index">
                                <option :value="index" x-text="club.name"></option>

                            </template>
                        </select>
                    </div>
                </div>

                <template x-if="seed_settings.use_seeds">
                    <div>
                        <h3>Seed</h3>
                        <div class="grid-4">
                            <template x-for="(event, index) in speed_events">
                                <div class="se-form-input">
                                    <label for="" x-text="event.name"
                                        x-show="seed_settings.seed_per_event"></label>
                                    <input type="text" x-mask="99:99.999" placeholder="01:23.456" name=""
                                        id="" x-model="add.team.seeds[event.id].seed">
                                    <small x-show="add.team.seeds[event.id]?.error">Seed must be empty or match
                                        format 12:34.567</small>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </form>





            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-outline-success" @click="createTeam()">Add</button>
            </x-slot>
        </x-s-e-modal>

        <x-s-e-modal id="editTeam" title="Edit Team">
            <p>Edits are applied instantly</p>
            <br>


            <div class="grid-2">
                <div class="se-form-input">
                    <input type="text" x-model="edit.team.name">
                </div>


                <div class="se-form-input">
                    <select name="" x-model="edit.team.league" id="">
                        <option value="null">No league</option>
                        <template x-for="[key, value] in Object.entries(league_map)" :key="key">
                            <option :value="key" x-text="value"></option>

                        </template>
                    </select>
                </div>
            </div>


            <template x-if="seed_settings.use_seeds">
                <div>
                    <h3>Seed</h3>
                    <div class="grid-4">
                        <template x-for="(event, index) in speed_events">
                            <div class="se-form-input">
                                <label for="" x-text="event.name" x-show="seed_settings.seed_per_event"></label>
                                <input type="text" x-mask="99:99.999" placeholder="01:23.456" name=""
                                    id="" x-model="edit.team.seeds[event.id].seed">
                                <small x-show="checkSeedTime(edit.team.seeds[event.id])">This seed will not be saved! It
                                    must be empty or match
                                    format 12:34.567</small>
                            </div>

                        </template>
                    </div>
                </div>
            </template>

        </x-s-e-modal>

        <x-s-e-modal id="addCompetitor" title="Add Competitor">
            <form x-ref="addCompetitor">
                <div class="grid-4">
                    <div class="se-form-input">
                        <input type="text" x-model="add.competitor.name" placeholder="Name" required>
                    </div>

                    <div class="se-form-input">
                        <select name="" x-model="add.competitor.league" id="">
                            <option value="null">No league</option>
                            <template x-for="[key, value] in Object.entries(league_map)" :key="key">
                                <option :value="key" x-text="value"></option>

                            </template>
                        </select>
                    </div>

                    <div class="se-form-input">
                        <select name="" id="" x-model="add.competitor.club">
                            <option value="null">No club</option>
                            <template x-for="(club, index) in data.clubs" :key="index">
                                <option :value="index" x-text="club.name" disabled
                                    :disabled="club.teams.length == 0"></option>

                            </template>
                        </select>
                    </div>


                    <div class="se-form-input">
                        <select name="" id="" x-model="add.competitor.team">
                            <option value="null">No Team
                            </option>
                            <template x-for="(team, index) in data.clubs[add.competitor.club]?.teams"
                                :key="index">
                                <option :value="index" x-text="team.name"></option>

                            </template>

                            <template x-if="add.competitor.club == null">
                                <template x-for="(team, index) in data.teams" :key="index">
                                    <option :value="index" x-text="team.name"></option>

                                </template>
                            </template>
                        </select>
                    </div>


                </div>


                <template x-if="seed_settings.use_seeds">
                    <div>
                        <h3>Seed</h3>
                        <div class="grid-4">
                            <template x-for="(event, index) in speed_events">
                                <div class="se-form-input">
                                    <label for="" x-text="event.name"
                                        x-show="seed_settings.seed_per_event"></label>
                                    <input type="text" x-mask="99:99.999" placeholder="01:23.456" name=""
                                        id="" x-model="add.competitor.seeds[event.id].seed">
                                    <small x-show="add.competitor.seeds[event.id]?.error">Seed must be empty or match
                                        format 12:34.567</small>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </form>


            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-outline-success" @click="createCompetitor()">Add</button>
            </x-slot>
        </x-s-e-modal>

        <x-s-e-modal id="editCompetitor" title="Edit Competitor">
            <p>Edits are applied instantly</p>
            <br>


            <div class="grid-2">
                <div class="se-form-input">
                    <input type="text" x-model="edit.competitor.name">
                </div>

                <div class="se-form-input">
                    <select name="" x-model="edit.competitor.league" id="">
                        <option value="null">No league</option>
                        <template x-for="[key, value] in Object.entries(league_map)" :key="key">
                            <option :value="key" x-text="value"></option>

                        </template>
                    </select>
                </div>
            </div>

            <template x-if="seed_settings.use_seeds">
                <div>
                    <h3>Seed</h3>
                    <div class="grid-4">
                        <template x-for="(event, index) in speed_events">
                            <div class="se-form-input">
                                <label for="" x-text="event.name" x-show="seed_settings.seed_per_event"></label>
                                <input type="text" x-mask="99:99.999" placeholder="01:23.456" name=""
                                    id="" x-model="edit.competitor.seeds[event.id].seed">
                                <small x-show="checkSeedTime(edit.competitor.seeds[event.id])">This seed will not be saved!
                                    It must be empty or match format 12:34.567</small>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </x-s-e-modal>



    </div>
@endsection
