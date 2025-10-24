@extends('layouts.competition')

@section('title')
    Overview
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
@endsection

@section('content')
    <div class="grid-3">
        <div>
            <h3>Events</h3>

            @can('access', [$comp, ['speed']])
                @foreach ($comp->getSpeedEvents as $event)
                    <a href="{{ route('comps.events.speeds.view', [$comp, $event]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">{{ $event->getName() }}</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </a>
                @endforeach
            @endcan

            @can('access', [$comp, ['serc', 'serc_writer']])
                @foreach ($comp->getSERCs as $event)
                    <a href="{{ route('comps.events.sercs.view', [$comp, $event]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo flex items-center">{{ $event->getName() }}
                            <span class="ml-2 badge badge-info badge-sm">SERC</span>
                        </p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </a>
                @endforeach
            @endcan

            <x-add-card link="{{ route('comps.events', $comp) }}" />




        </div>

        <div>
            <h3>Leagues/Brackets</h3>


            @foreach ($comp->getLeagues as $league)
                <a href="{{ route('comps.leagues.view', [$comp, $league]) }}"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo flex items-center">{{ $league->name }}
                    </p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>
            @endforeach


            <x-add-card link="{{ route('comps.leagues.create', $comp) }}" />




        </div>

        @can('access', [$comp, 'admin'])
            <div>
                <h3>Digital Judge</h3>

                @if ($comp->digitalJudgeEnabled)
                    <div class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                        @click="modals.djPin = true">
                        <p class="font-archivo">Pins</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </div>

                    <a href="{{ route('dj.qrs', $comp) }}" target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">QR Code</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </a>

                    <a href="{{ $comp->resolveJudgeLogVersionUrl() }}""
                        class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Log</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </a>

                    <hr class="spacer">

                    <a href="https://docs.google.com/document/d/1HKTR9HUzgTKadyE7vyVqDWXeaheK4XFmzlw9Hrn1Q1s/edit?usp=sharing"
                        target="_blank" rel="noopener noreferrer"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Help (Manual)</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </a>


                    <div @click="modals.djSettings = true"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1 mb-2">
                        <p class="font-archivo">Settings</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>


                    </div>

                    <a href="{{ route('dj.toggle', $comp) }}" class="se-btn se-btn-danger block ">Disable</a>
                @else
                    <p class="mb-2">DigitalJudge allows officials to enter SERC marks on their own devices.</p>
                    <a href="{{ route('dj.toggle', $comp) }}" class="se-btn se-btn-success block">Enable</a>
                @endif







            </div>

            <div>
                <h3>Competition</h3>
                <div @click="modals.compSettings = true"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Settings</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </div>

                <div @click="modals.compAccounts = true"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Additional Accounts</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </div>

                <div @click="modals.compDelete = true"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1 w-full">
                    <p class="font-archivo">Delete Competition</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>





                </div>

            </div>
        @endcan
    </div>

    @can('access', [$comp, 'admin'])
        <x-s-e-modal id="djPin" title="Pins">
            <p class="mb-4">Officials should navigate to <a
                    href="{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') }}"
                    class="link">{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') }}
                </a> and enter one of the following:</p>


            <div class="grid-2">
                <div>
                    <h4>Judges</h4>
                    <p class="font-semibold text-lg">{{ $comp->digitalJudgePin }}</p>

                </div>

                <div>
                    <h4>Head Referee</h4>
                    <p class="font-semibold text-lg">{{ $comp->digitalJudgeHeadPin }}</p>
                </div>
            </div>
        </x-s-e-modal>

        <x-s-e-modal id="djSettings" title="DigitalJudge Settings">

            <form id="djSettingsForm"
                x-on:submit="(e) => {
            e.preventDefault()
         

          if (this.loading) {
                return
            }

               this.loading = true
            


            fetch('{{ route('dj.settings', $comp) }}', {
                method: 'POST',
                body: new FormData($event.target),
            }).then(resp => {
               this.loading = false
                if (!resp.ok) {
                    showAlert('Something went wrong, you changes have been reversed.')
                    $event.target.reset()
                    return
                }
     

                loading = false
                modals.djSettings = false
                showSuccess('DigitalJudge settings saved')
            })
        }">


                @csrf
                <h4>Enabled Events</h4>
                <p>Please check all the events below that you want to enable DigitalJudge for:</p>
                <div class="ml-3 mt-1 mb-6">



                    @foreach ($comp->getSpeedEvents as $event)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="sp:{{ $event->id }}"
                                @if ($event->digitalJudgeEnabled) checked @endif id="sp:{{ $event->id }}">
                            <label for="sp:{{ $event->id }}"
                                class="font-archivo flex items-center">{{ $event->getName() }}</label>
                        </div>
                    @endforeach

                    @foreach ($comp->getSERCs as $event)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="se:{{ $event->id }}"
                                @if ($event->digitalJudgeEnabled) checked @endif id="se:{{ $event->id }}">
                            <label for="se:{{ $event->id }}" class="font-archivo flex items-center">
                                {{ $event->getName() }}
                                <span class="ml-2 badge badge-info badge-sm">SERC</span>
                            </label>
                        </div>
                    @endforeach


                </div>

                <h4>Other</h4>


                <div class="flex space-x-2 items-start">
                    <input type="checkbox" name="show_teams_to_judges" @if ($comp->show_teams_to_judges) checked @endif
                        id="show_teams_to_judges" class="mt-[0.375rem] ml-3">

                    <label for="show_teams_to_judges" class="flex flex-col">
                        Show Team names
                        <small>If enabled, Judges will see team names when marking instead of the number</small>
                    </label>
                </div>
            </form>
            @slot('footer')
                <button type="submit" form="djSettingsForm" class="se-btn se-btn-success ml-auto">Save</button>
            @endslot
        </x-s-e-modal>

        <x-s-e-modal id="compSettings" title="Settings" class="se-modal-wide ">



            <div class="se-pannel" id="competition_settings" x-data="{
                active: 'details',
            
                form: {
                    changes: false,
                    data: {},
            
                },
            
            
                async validateForm() {
            
                    if (!this.form.changes) {
                        console.log('no changes')
                        return
                    }
            
                    for ([name, data] of Object.entries(this.form.data)) {
                        if (data.error) {
                            this.active = data.section
                            this.$nextTick(() => {
                                data.element.focus()
                            })
                            return
                        }
                    }
            
                    let form_data = {}
            
                    for ([name, data] of Object.entries(this.form.data)) {
                        form_data[name] = data.data
                    }
            
                    let response = await fetch('{{ route('comps.settings', $comp) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(form_data)
                    })
            
                    if (!response.ok) {
                        showAlert('Failed to save settings. Check your inputs')
                        return
                    }
            
                    showSuccess('Competition settings saved')
            
                    this.form.changes = false
                    this.$nextTick(() => this.shared.changes = false)
            
                    this.modals.compSettings = false
            
                },
            
                init() {
                    shared.submit = () => {
                        this.validateForm()
                    }
            
                    $watch('form.changes', (v) => shared.changes = true)
            
                },
            
            
            
            
            }" @input="form.changes = true">
                <ul class="">
                    <li :class="active == 'details' ? 'active' : ''" @click="active = 'details'">Details</li>
                    <li :class="active == 'setup' ? 'active' : ''" @click="active = 'setup'">Setup</li>
                    <li :class="active == 'hd' ? 'active' : ''" @click="active = 'hd'">Heats & Draws</li>
                    <li :class="active == 'names' ? 'active' : ''" @click="active = 'names'">Name Formatting</li>

                </ul>


                <div x-cloak x-show="active == 'details'">
                    <h3>Details</h3>

                    <div class="grid-2 w-full">

                        @csrf
                        <x-se-input label="Name" name="name" :default="$comp->name" required minlength="3"
                            section="details" />

                        <x-se-input label="Date" name="when" type="date" :default="$comp->when->format('Y-m-d')" required
                            section="details" />

                        <x-se-input label="Location" name="where" :default="$comp->where" required section="details" />


                    </div>

                </div>
                <div x-cloak x-show="active == 'setup'">
                    <h3>Setup</h3>

                    <div class="grid-2 w-full">


                        @csrf

                        <x-se-input label="Lanes" name="max_lanes" type="number" min="1" :default="$comp->max_lanes" required
                            section="setup" />


                        @php
                            $sercStart = $comp->serc_start_time;

                            $sercStart?->setSeconds(0);
                        @endphp


                        <x-se-input label="SERC Start Time" name="serc_start_time" type="datetime-local" :default="$sercStart"
                            required section="setup" />

                        <x-se-input name="timezone" type="hidden" x-init="self.data = Intl.DateTimeFormat().resolvedOptions().timeZone;" section="setup" />


                        <x-se-input label="Viewable Live" name="can_be_live" type="checkbox" :default="$comp->can_be_live"
                            section="setup" />


                    </div>

                </div>

                <div x-cloak x-show="active == 'hd'">
                    <h3>Heats & Draws</h3>

                    <div class="grid-2 w-full">


                        @csrf

                        <x-se-input label="Tanks" name="ss:use_tanks" type="select" section="hd" :default="$comp->getScoringSettings->use_tanks">
                            <option value="1" @if ($comp->getScoringSettings->use_tanks) selected @endif>Yes</option>
                            <option value="0" @if (!$comp->getScoringSettings->use_tanks) selected @endif>No</option>
                        </x-se-input>

                        <x-se-input label="Use Seed Times" name="use_seeds" type="select" section="hd" :default="$comp->use_seeds">
                            <option value="1" @if ($comp->use_seeds) selected @endif>Yes</option>
                            <option value="0" @if (!$comp->use_seeds) selected @endif>No</option>
                        </x-se-input>

                        <x-se-input label="Heats Per Event" name="heats_per_event" type="select" section="hd"
                            :default="$comp->heats_per_event">
                            <option value="1" @if ($comp->heats_per_event) selected @endif>Yes</option>
                            <option value="0" @if (!$comp->heats_per_event) selected @endif>No</option>
                        </x-se-input>

                        <x-se-input label="Seed Per Event" name="seed_per_event" type="select" section="hd"
                            :default="$comp->seed_per_event">
                            <option value="1" @if ($comp->seed_per_event) selected @endif>Yes</option>
                            <option value="0" @if (!$comp->seed_per_event) selected @endif>No</option>
                        </x-se-input>


                    </div>

                </div>


                <div x-cloak x-show="active == 'names'">
                    <h3>Name Formatting</h3>

                    <p>Name formatting allows you to customise how Team and Competitor names are displayed. The identifiers
                        lsited in <strong>BOLD</strong> can be included and will be replaced with the associated
                        name/identifier. An formatted example is displayed below each input
                    </p>
                    <br>

                    <div class="grid-2 w-full">


                        @csrf

                        <div x-data="{
                            format: '{{ $comp->team_format }}',
                        
                        
                            sample: {
                                club: 'Club',
                                league: 'Youth',
                                name: 'Team',
                                competitors: 'Noah, Kirsty'
                            },
                        
                            getSample() {
                                return this.format
                                    .replace(/:C/g, this.sample.club)
                                    .replace(/:L/g, this.sample.league)
                                    .replace(/:N/g, this.sample.name)
                                    .replace(/:S/g, this.sample.competitors);
                            },
                        
                            init() {
                                $watch('form.data.team_foramt', (v) => {
                                    this.format = v.data
                                })
                            }
                        }">

                            <x-se-input label="Team Name Format" name="team_format" :default="$comp->team_format" required
                                section="names">
                                <x-slot name="description">
                                    <p><strong>:C</strong> - Club, <strong>:L</strong> - League, <strong>:N</strong> - Name,
                                        <strong>:S</strong> - Competitor Names,
                                    </p>
                                </x-slot>
                            </x-se-input>

                            <small class="text-gray-500!" x-text="getSample">example</small>

                        </div>



                        <div x-data="{
                            format: '{{ $comp->team_format }}',
                        
                        
                            sample: {
                                club: 'Club',
                                league: 'Youth',
                                name: 'Noah',
                                team: 'Team'
                            },
                        
                            getSample() {
                                return this.format
                                    .replace(/:C/g, this.sample.club)
                                    .replace(/:L/g, this.sample.league)
                                    .replace(/:N/g, this.sample.name)
                                    .replace(/:T/g, this.sample.team);
                        
                        
                            },
                        
                            init() {
                                $watch('form.data.competitor_foramt', (v) => {
                                    this.format = v.data
                                })
                            }
                        }">

                            <x-se-input label="Competitor Name Format" name="competitor_format" :default="$comp->competitor_format" required
                                section="names">
                                <x-slot name="description">
                                    <p><strong>:C</strong> - Club, <strong>:L</strong> - League, <strong>:N</strong> - Name,
                                        <strong>:T</strong> - Team,
                                    </p>
                                </x-slot>
                            </x-se-input>

                            <small class="text-gray-500!" x-text="getSample">example</small>

                        </div>




                    </div>

                </div>

                <x-slot name="left_footer">
                    <small class="  text-red-500   mr-auto! animate-pulse" x-show="shared.changes">You have unsaved
                        changes!</small>
                </x-slot>


            </div>





            <x-slot name="footer">
                <button form="competition_settings" class="se-btn se-btn-success" @click="shared.submit()">Save</button>
            </x-slot>
        </x-s-e-modal>


        <x-s-e-modal id="compAccounts" title="Additional Accounts">

            <div class="se-table" x-data="{
                accounts: [],
            
                getAccounts() {
                    fetch('{{ route('comps.accounts', $comp) }}')
                        .then(resp => resp.json())
                        .then(data => {
                            this.accounts = data;
            
                        })
                        .catch(err => {
                            console.error('Failed to fetch accounts:', err);
                            showAlert('Failed to load accounts');
                        });
                },
            }" x-init="$watch('modals.compAccounts', value => value ? getAccounts() : null)">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="account in accounts">
                            <tr
                                @click="() => {modals.data.compEditAccount.id = account.id; modals.compEditAccount = true; modals.compAccounts = false}">
                                <th><span x-text="account.name"></span></th>
                                <td
                                    x-text="account.access.length > 2 ? `${account.access.slice(0,2).join(', ')} & ${account.access.length - 2} more` : account.access.join(', ')">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <br>

            <button class="se-btn se-btn-success"
                @click="() => {modals.compAddAccount = true; modals.compAccounts = false}">Add
                Account</button>


        </x-s-e-modal>


        <x-s-e-modal id="compAddAccount" title="Add Account">

            <form id="compAddAccountForm" x-data="{
                accounts: [],
                email: '',
            
                searchEmail() {
                    email = this.email.trim()
                    if (email.length < 3) {
                        return
                    }
            
                    fetch('{{ route('accounts.search', '__id') }}'.replace('__id', email)).then(resp => resp.json()).then(data => {
                        this.accounts = data
                    })
                }
            }"
                x-on:submit="(e) => {
            e.preventDefault()
           
            
            if (this.loading) {
                return
            }

            this.loading = true


            fetch('{{ route('comps.accounts.invite', $comp) }}', {
                method: 'POST',
                body: new FormData($event.target),
                headers: {
                    'Accept': 'application/json',
                    
                }
            }).then(resp => {
                this.loading = false
                if (!resp.ok && resp.status != 422) {
                    showAlert('Something went wrong, account not invited.')
             
                    return
                }

              
                return resp.json()

               
            }).then(data => {

                
                if (data.errors) {
                    showAlert(Object.entries(data.errors)[0][1])
                    return
                }

                if (data.error) {
                    showAlert(data.error)
                    return
                }

                loading = false
                modals.compAddAccount = false
                modals.compAccounts = true
                $event.target.reset()
                showSuccess('Account invited')
            })
        }"
                x-init="() => {
                    onClose = () => {
                        modals.compAccounts = true
                    }
                }">


                @csrf


                <p class="text-sm">Invite someone to this organisaiton. If they have an account it will appear as you type
                    their email,
                    otherwise they will be invited to create an account.</p>
                <br>

                <div class="se-form-input relative">
                    <input type="text" name="email" id="email" required placeholder="Email Address"
                        x-model="email" @keyup.debounce.200ms="searchEmail">



                    <div class="absolute w-full top-2/3 left-0  bg-white border" x-show="accounts.length > 0" x-cloak>
                        <template x-for="account in accounts">
                            <div class="se-card se-card-hover p-2! text-sm! flex-row! rounded-none! items-center justify-between"
                                @click="() => {email = account.email; accounts = []}">
                                <span x-text="account.name"></span>
                                <small x-text="account.email"></small>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid-3 gap-1!">
                    @foreach (App\Models\Competition::$accessTypes as $type => $name)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="access[]" value="{{ $type }}"
                                id="access-{{ $type }}">
                            <label for="access-{{ $type }}" class="font-archivo flex items-center">
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                </div>







            </form>

            <x-slot name="footer">
                <button type="submit" form="compAddAccountForm" class="se-btn se-btn-success ml-auto">Add</button>
            </x-slot>
        </x-s-e-modal>


        <x-s-e-modal id="compEditAccount" title="Edit Account">

            <form id="compEditAccountForm"
                x-on:submit="(e) => {
            e.preventDefault()
           
            
            if (this.loading) {
                return
            }

            this.loading = true


            fetch('{{ route('comps.accounts.edit', [$comp, 'account' => '__id']) }}'.replace('__id', modals.data.compEditAccount?.id), {
                method: 'POST',
                body: new FormData($event.target),
                headers: {
                    'Accept': 'application/json',
                    
                }
            }).then(resp => {
                this.loading = false
                if (!resp.ok && resp.status != 422) {
                    showAlert('Something went wrong, unable to save changes')
                  
                    return null
                }

              
                return resp.json()

               
            }).then(data => {

                

                if (data.errors) {
                    showAlert(Object.entries(data.errors)[0][1])
                    return
                }

                closeModal()
                
                showSuccess('Account updated')
            })
        }"
                x-data="{
                    data: {
                        name: '',
                
                        access: [],
                        owner: false,
                    },
                
                    fetchAccount(id) {
                        this.loading = true
                        fetch('{{ route('comps.accounts.view', [$comp, 'account' => '__id']) }}'.replace('__id', id))
                            .then(resp => resp.json())
                            .then(rdata => {
                
                                if (rdata.error) {
                                    showAlert(rdata.error);
                                    return;
                                }
                
                                this.data.name = rdata.name;
                                this.data.email = rdata.email;
                
                                rdata.access.forEach(access => {
                                    let target = $el.querySelector(`#edit-access-${access}`)
                                    if (target) {
                                        target.checked = true
                                    }
                
                
                                })
                
                                this.data.owner = rdata.access.includes('owner')
                
                
                            })
                            .catch(err => {
                                console.error('Failed to fetch account:', err);
                                showAlert('Failed to load account details');
                            }).finally(() => {
                                this.loading = false;
                            });
                    },
                
                    deleteAccount() {
                
                        if (this.loading) {
                            return;
                        }
                
                        if (this.data.owner) {
                            alert('You cannot remove yourself');
                            return
                        }
                
                        if (!confirm('Are you sure you want to remove this account? This cannot be undone.')) {
                            return;
                        }
                
                        this.loading = true
                
                        fetch('{{ route('comps.accounts.delete', [$comp, 'account' => '__id']) }}'.replace('__id', modals.data.compEditAccount?.id), {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(resp => {
                            if (!resp.ok) {
                                showAlert('Something went wrong, unable to delete account')
                                return;
                            }
                
                            showSuccess('Account deleted')
                            modals.compAccounts = true
                            modals.compEditAccount = false
                        }).finally(() => {
                            this.loading = false;
                        });
                    }
                
                }" x-init="() => {
                
                    onClose = () => {
                        modals.compAccounts = true
                    }
                
                    $watch('modals.data.compEditAccount?.id', value => {
                        if (value == undefined) {
                            return;
                        }
                
                        fetchAccount(value)
                
                    })
                
                    window.addEventListener('delete-account', () => deleteAccount())
                }">


                @csrf

                <h3 x-text="data.name"></h3>
                <p class="text-sm text-gray-400 mb-4" x-text="data.email"></p>



                <div class="grid-4 gap-1!" x-show="!data.owner">
                    @foreach (App\Models\Competition::$accessTypes as $type => $name)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="access[]" value="{{ $type }}"
                                id="edit-access-{{ $type }}">
                            <label for="edit-access-{{ $type }}" class="font-archivo flex items-center">
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <p x-show="data.owner">
                    You own this competition
                </p>


            </form>

            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-danger"
                    @click.stop="window.dispatchEvent(new CustomEvent('delete-account'))">Remove</button>
                <button type="submit" form="compEditAccountForm" class="se-btn se-btn-success ml-auto">Save</button>


            </x-slot>
        </x-s-e-modal>


        <x-s-e-modal id="compDelete" title="Delete Competition">

            <div x-data="{
                name: '',
                targetName: '{{ $comp->name }}',
            
                deleteCompetition() {
            
                    if (this.name != this.targetName) {
                        return
                    }
            
                    if (!confirm('Are you sure, everything will be removed!')) {
                        return
                    }
            
                    let fd = new FormData()
                    fd.append('name', this.name)
            
            
                    fetch('{{ route('comps.delete', $comp) }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: fd,
                        method: 'POST'
                    }).then(resp => resp.json()).then(data => {
                        if (data.error) {
                            showAlert(data.error)
                            return
                        }
            
                        window.location.href = '/'
                    })
            
                }
            }">
                <div class="alert-box">
                    <h1>Warning</h1>
                    <p>This cannot be undone!</p>
                </div>
                <br>
                <div class="se-form-input">
                    <label for="comp-del-name">Confirm Competition Name</label>
                    <input type="text" id="comp-del-name" name="name" placeholder="{{ $comp->name }}"
                        x-model="name">
                    <small x-show="name != targetName">Competition name doesn't match</small>

                </div>

                <button class="se-btn se-btn-danger" @click="deleteCompetition">Delete Competition</button>
            </div>


        </x-s-e-modal>
    @endcan
@endsection
