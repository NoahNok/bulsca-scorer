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
@endsection
