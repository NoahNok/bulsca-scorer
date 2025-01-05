@extends('layout')

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
    <div class="flex items-center justify-between">
        <div>

            <h2 class="@if (!$comp->brand) mb-0 @endif">{{ $comp->name }}</h2>

            <div class="flex  items-center text-sm">
                @if ($comp->brand)
                    <img src="{{ $comp->getBrand->getLogo() }}" alt="{{ $comp->getBrand->name }}"
                        class="max-w-[20px] max-h-[20px] ">
                    <p class="mb-0 ml-[2px]">{{ $comp->getBrand->name }} </p>


                    <span class="w-1 h-1 bg-black rounded-full mx-2"></span>
                @endif

                <small class="text-gray-500">Scoring v{{ $comp->scoring_version }}</small>
            </div>


        </div>


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

                <x-settings-cog href="{{ route('comps.settings', $comp) }}" />
            </div>
        @endcan



    </div>


    <p class="mt-2">Welcome to the scorer for {{ $comp->name }}. If you run into any issues please contact the Data
        Manager (<a class="link" href="mailto:data@bulsca.co.uk">data@bulsca.co.uk</a>) or find them on the day!

    </p>
    <br>
    <div class="grid-5">
        @can('access', $comp)
            @if (\App\Helpers\ScoringHelper::getCompetitionScoringDetails($comp)['use_competitors'])
                <a href="{{ route('comps.view.competitors', $comp) }}"
                    class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                    <p class="text-lg font-semibold">Competitors</p>
                </a>
            @else
                <a href="{{ route('comps.view.teams', $comp) }}"
                    class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                    <p class="text-lg font-semibold">Teams</p>
                </a>
            @endif
        @endcan

        @can('access', [$comp, '*'])
            <a href="{{ route('comps.view.heats', $comp) }}"
                class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                <p class="text-lg font-semibold">Heats/Orders</p>
            </a>
        @endcan
        @can('access', $comp)
            <a href="{{ route('comps.view.printables', $comp) }}"
                class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                <p class="text-lg font-semibold">Printables</p>
            </a>
            <a href="{{ route('comps.view.events', $comp) }}"
                class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                <p class="text-lg font-semibold">Events</p>
            </a>
            <a href="{{ route('comps.view.results', $comp) }}"
                class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
                <p class="text-lg font-semibold">Results</p>
            </a>
        @endcan

    </div>
    <br>
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
            <div class="card">
                <div class="flex items-center justify-between">
                    <h3>Digital Judging</h3>
                    @if ($comp->digitalJudgeEnabled)
                        <x-settings-cog href="{{ route('dj.settings', $comp) }}" />
                    @endif
                </div>


                <p>Digital judging allows Judges to enter SERC marks on their own device. It is enabled per comp.</p>
                <strong>If you need to DQ a SERC team, please talk with the competitions Scorer or Organiser!</strong>
                <p><a href="https://docs.google.com/document/d/1HKTR9HUzgTKadyE7vyVqDWXeaheK4XFmzlw9Hrn1Q1s/edit?usp=sharing"
                        target="_blank" rel="noopener noreferrer" class="link">DigitalJudge Manual</a></p>
                <br>
                @if ($comp->digitalJudgeEnabled)
                    <h5>Judges</h5>
                    <a href="{{ route('dj.qrs', $comp) }}" target="_blank" class="btn btn-thin btn-primary ">Print QR</a>
                    <p class="text-center font-semibold text-bulsca_red">OR</p>
                    <p>Please instruct Judges to go to here: <a
                            href="{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') . ($comp->brand ? '?b=' . $comp->brand : '') }}"
                            class="link">{{ \App\Helpers\RouteHelpers::externalRoute('judge', 'dj.index') . ($comp->brand ? '?b=' . $comp->brand : '') }}
                        </a> and enter the following pin:</p>

                    <p class="text-xl"><strong>{{ $comp->digitalJudgePin }}</strong></p>

                    <br>
                    <h5>Head Judge/SERC Setter</h5>
                    <p>Please instruct your Head Judge and SERC Setters to follw the same above link, but use the following pin
                        instead:</p>
                    <p class="text-xl"><strong>{{ $comp->digitalJudgeHeadPin }}</strong></p>
                    <br>
                    <p>This pin grants the ability for your Head Judge and SERC Setters to override Judge scores incase they
                        accidently make a mistake</p>
                    <br>

                    <h5>Judging Log</h5>
                    <p><a class="link" href="{{ $comp->resolveJudgeLogVersionUrl() }}">Click here</a> to view an activity
                        log of all
                        judge activity.</p>

                    <br>

                    <a href="{{ route('dj.toggle', $comp) }}" class="btn btn-danger">Disable Digital Judging</a>
                @else
                    <a href="{{ route('dj.toggle', $comp) }}" class="btn">Enable Digital Judging</a>
                @endif
            </div>

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
                <div class="card !grow-0">
                    <div class="flex items-center justify-between">
                        <h3>Additional Accounts</h3>
                    </div>


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




    <h3>Important Notes</h3>
    <div class="grid-4">
        <div>
            <h4>Results</h4>
            <ul class=" list-disc list-inside space-y-1">
                <li>The setup of a results sheet cannot be altered after creation. If you need to change a weighting, you'll
                    need to create a new sheet and delete the old one.</li>
                <li>Results sheets automatically update when you change scores so you don't need to worry about having to
                    recreate the sheet every time you make a change to a result.</li>
            </ul>

        </div>
        <div>
            <h4>Time Format</h4>
            <ul class=" list-disc list-inside space-y-1">
                <li>When entering a time please enter it in the following format: <code>xx:xx.xx</code>, where each x is a
                    digit between 0-9!</li>
                <li>You may omit leading zeros for the minute and seconds but you must include both digits for millis.</li>

            </ul>
        </div>
        <div>
            <h4>Multiple Penalties</h4>
            <ul class=" list-disc list-inside space-y-1">
                <li>If an event allows for multiple penalties, then they must be entered as a comma (,) separated list.</li>
                <li>Penalties should take the form <code>Pxxx</code> with x being a digit. <strong>They must</strong> be 3
                    characters long!</li>
            </ul>
        </div>
        <div>
            <h4>Disqualifications</h4>
            <ul class=" list-disc list-inside space-y-1">
                <li><strong>Only one</strong> disqualification should be entered into the disqualification box.</li>
                <li>They should be in the form of <code>DQxxx</code> with x being a digit and there must be 3 digits!</li>
                <li>DQ501 relating to excessive penalties <strong>MUST</strong> be left out as the system automatically
                    applies it to
                    relevant
                    events (Swim & Tow)</li>
            </ul>
        </div>
    </div>
@endsection
