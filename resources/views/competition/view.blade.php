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
            </div>
        @endcan
    </div>

    @can('access', [$comp, 'admin'])
        <x-s-e-modal id="djPin" title="Pins">
            <p class="mb-4">Officials should navigate to <a
                    href="{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') . ($comp->brand ? '?b=' . $comp->brand : '') }}"
                    class="link">{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') . ($comp->brand ? '?b=' . $comp->brand : '') }}
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
                            <label for="sp:{{ $event->id }}">{{ $event->getName() }}</label>
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

        <x-s-e-modal id="compSettings" title="Competition Settings">

            <form id="compSettingsForm"
                x-on:submit="(e) => {
            e.preventDefault()
         

              if (this.loading) {
                return
            }
               this.loading = true
            


            fetch('{{ route('comps.settings', $comp) }}', {
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
                modals.compSettings = false
                showSuccess('Competition settings saved')
            })
        }">


                @csrf

                <h4>Heats</h4>
                <x-form-input id="lanes" title="Lanes" type="number" defaultValue="{{ $comp->max_lanes }}" />




                <h4>Other</h4>
                @php
                    $sercStart = $comp->serc_start_time;
                    $sercStart?->setTimezone('BST');
                @endphp
                <x-form-input id="serc_start_time" title="SERC Start Time" required="false" type="datetime-local"
                    defaultValue="{{ $sercStart }}"></x-form-input>

                <div class="flex space-x-2 -mt-5!">
                    <input type="checkbox" class="ml-3" name="can_be_live" @if ($comp->can_be_live) checked @endif
                        id="can_be_live">
                    <label for="can_be_live">Viewable Live</label>
                </div>


            </form>

            <x-slot name="footer">
                <button type="submit" form="compSettingsForm" class="se-btn se-btn-success ml-auto">Save</button>
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

            <form id="compAddAccountForm"
                x-on:submit="(e) => {
            e.preventDefault()
           
            
            if (this.loading) {
                return
            }

            this.loading = true


            fetch('{{ route('comps.accounts.create', $comp) }}', {
                method: 'POST',
                body: new FormData($event.target),
                headers: {
                    'Accept': 'application/json',
                    
                }
            }).then(resp => {
                this.loading = false
                if (!resp.ok && resp.status != 422) {
                    showAlert('Something went wrong, account was not created.')
             
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
                showSuccess('Competition settings saved')
            })
        }"
                x-init="() => {
                    onClose = () => {
                        modals.compAccounts = true
                    }
                }">


                @csrf


                <div class="se-form-input">
                    <input type="text" name="name" id="name" required placeholder="Name">
                </div>

                <div class="se-form-input">
                    <input type="text" name="email" id="email" required placeholder="Email Address">
                </div>

                <div class="grid-4 gap-1!">
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
                
                                rdata.access.forEach(access => {
                                    $el.querySelector(`#edit-access-${access}`).checked = true;
                                })
                
                
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
                
                
                
                        if (!confirm('Are you sure you want to delete this account? This cannot be undone.')) {
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


                <div class="grid-4 gap-1!">
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


            </form>

            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-danger"
                    @click.stop="window.dispatchEvent(new CustomEvent('delete-account'))">Delete</button>
                <button type="submit" form="compEditAccountForm" class="se-btn se-btn-success ml-auto">Save</button>


            </x-slot>
        </x-s-e-modal>
    @endcan
@endsection
