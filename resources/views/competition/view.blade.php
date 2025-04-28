@extends('layouts.competition')

@section('title')
    {{ $comp->name }}
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

            @foreach ($comp->getSpeedEvents as $event)
                <a href="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">{{ $event->getName() }}</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>
            @endforeach

            @foreach ($comp->getSERCs as $event)
                <a href="{{ route('comps.view.events.sercs.view', [$comp, $event]) }}"
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




        </div>


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

            <div class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                <p class="font-archivo">Additional Accounts</p>



                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


            </div>
        </div>
    </div>


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
            loading = true
            


            fetch('{{ route('dj.settings', $comp) }}', {
                method: 'POST',
                body: new FormData($event.target),
            }).then(resp => {
               loading = false
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
            loading = true
            


            fetch('{{ route('comps.settings', $comp) }}', {
                method: 'POST',
                body: new FormData($event.target),
            }).then(resp => {
               loading = false
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





    <br>
    <br>
    <br>




    <div class="flex items-center justify-between">



        @can('access', $comp)
            <div class="flex items-center justify-center">
                <a href="{{ route('comps.notifications.user-settings', $comp) }}"
                    class=" transition-color cursor-pointer text-gray-600 hover:text-white hover:bg-bulsca rounded-full p-[0.375rem]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </a>


            </div>
        @endcan



    </div>



    <div>
        <h3>Scorer Manual</h3>
        <p>You can find the scorer manual <a class="link" target="_blank" rel="noopener noreferrer"
                href="https://docs.google.com/document/d/1P1XMiKYkcwFP9gp-GMf7Uj7DJhRER65IVTUDG-sJu5o/edit?usp=sharing">here
                (Google Drive)</a>
            or <a class="link" target="_blank" rel="noopener noreferrer"
                href="https://www.bulsca.co.uk/resources/view/7851d57a-e23a-4e83-bdfd-58df662748a5">here (PDF)</a>
        </p>
    </div>
    <br>
    <hr>
    <br>

    @can('access', $comp)
        <div class="grid-3">


            @if ($comp->digitalJudgeEnabled && $comp->getMaxHeats() == -1)
                <div>
                    <div class="alert-box">
                        <p class="font-semibold">No Heats Set</p>
                        <p class="text-sm">You have not generated heats any yet. You will not be able to digitally judge any
                            speeds
                            events until you do so!</p>
                    </div>
                </div>
            @endif


            <div>
                <div class="card grow-0!">
                    <div class="flex items-center justify-between">
                        <h3>Additional Accounts</h3>
                    </div>

                    <p>For assistance please see the <strong>"SERC Writer Login"</strong> section of the <a class="link"
                            target="_blank" rel="noopener noreferrer"
                            href="https://docs.google.com/document/d/1P1XMiKYkcwFP9gp-GMf7Uj7DJhRER65IVTUDG-sJu5o/edit?usp=sharing#heading=h.3dqfvnjctnuo">Scorer
                            Manual</a></p>
                    <br>

                    <div class="  relative w-full  ">
                        <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                            <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                                <tr>
                                    <th scope="col" class="py-3 px-6 text-left">
                                        Name
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Email
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Actions
                                    </th>

                                </tr>
                            </thead>
                            <tbody>


                                <tr class="bg-white border-b text-right " x-data="{
                                    openModal: false,
                                    data: null,
                                
                                
                                    createAccount() {
                                
                                        fetch('{{ route('comps.accounts.serc-writer.create', $comp) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        }).then(resp => resp.json()).then(data => {
                                            if (data?.error) {
                                                alert(data.error)
                                                return
                                            }
                                
                                            this.data = data
                                            this.openModal = true
                                        })
                                
                                
                                    },
                                
                                    resetAccountPassword() {
                                        fetch('{{ route('comps.accounts.serc-writer.new-password', $comp) }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            }
                                        }).then(resp => resp.json()).then(data => {
                                            if (data?.error) {
                                                alert(data.error)
                                                return
                                            }
                                
                                            this.data = data
                                            this.openModal = true
                                        })
                                    }
                                }">
                                    <th scope="row"
                                        class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                        SERC Writer
                                    </th>

                                    @php
                                        $swa = $comp->getSercWriterAccount();
                                    @endphp

                                    <td class="py-4 px-6 ">
                                        <p>{{ $swa?->email ?? '-' }}</p>
                                    </td>


                                    <td class="py-4 px-6 ">
                                        <div class="flex items-end justify-end space-x-3 relative">




                                            @if ($swa)
                                                <div title="Reset Password" @click="resetAccountPassword">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6 hover:text-black cursor-pointer">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div title="Create Account" @click="createAccount">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6 hover:text-black cursor-pointer">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>

                                                </div>
                                            @endif


                                            <div class="relative">
                                                <div class="modal" x-cloak x-show="openModal">
                                                    <div class="modal-content">
                                                        <div class="flex flex-col text-left text-black">
                                                            <h4>SERC Writer Account</h4>
                                                            <p>Please give the following details to your SERC Writer(s) so that
                                                                they
                                                                can login and setup their SERCs.</p>
                                                            <br>

                                                            <p>
                                                                <strong>Email</strong>
                                                                <br>
                                                                <span x-text="data?.email"></span>
                                                                <br>
                                                                <strong>Password</strong>
                                                                <br>
                                                                <span x-text="data?.password"></span>
                                                            </p>
                                                            <br>
                                                            <button class="btn btn-danger"
                                                                @click="() => { window.location.reload() }">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>




                                        </div>




                                    </td>

                                </tr>




                            </tbody>
                        </table>
                    </div>

                </div>
            </div>



        </div>
        <br>
        <hr>
        <br>
    @endcan





@endsection
