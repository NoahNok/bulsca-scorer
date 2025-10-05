@extends('layouts.competition')

@section('title')
    Results
@endsection

@section('content')
    @if (!DigitalJudge::canGenerateCompetitionResults($comp))
        <div class="alert-box">

            <h1>Unable to create results!</h1>
            <p><strong>You cannot generate results</strong> until Digitally Judged SERC's have had their marks confirmed by
                the Head Judge.
                <br>
                <br>
                The following SERCs require confirming: @forelse (DigitalJudge::getSercsRequiringConfirmation($comp) as $serc)
                    <a href="{{ route('comps.events.sercs.view', [$comp, $serc]) }}" class="link">{{ $serc->name }}</a>
                @empty
                    None
                @endforelse
                <br>
                The following speeds require confirming: @forelse (DigitalJudge::getSpeedsRequiringConfirmation($comp) as $speed)
                    <a href="{{ route('comps.events.speeds.view', [$comp, $speed]) }}"
                        class="link">{{ $speed->getName() }}</a>
                @empty
                    None
                @endforelse

                <br>
                <br>
                If you weren't expecting a SERC to be Digitally Judged, go to the SERC's settings and disable DigitalJudge,
            </p>

        </div>
        <br>
    @endif


    <div class="flex items-center justify-between">
        <h2>Results</h2>
        <a href="{{ route('comps.results.print-all', $comp) }}" target="_blank"
            class="se-btn @if ($comp->scoring_type != 'rlss-nationals') se-btn-disabled @endif">Print All</a>
    </div>

    <p><strong>Do not</strong> make a results sheet until you have made all your events! (You cannot edit which events are
        part of a results sheet after it has been made!)</p>
    <br>


    <div class="grid-4">
        @foreach ($comp->getResultSchemas as $schema)
            <a href="{{ route('comps.results.view-schema', ['comp' => $comp, 'schema' => $schema]) }}" class="se-btn">
                <p class="text-lg font-semibold">{{ $schema->name }}</p>


            </a>
        @endforeach
        @if (DigitalJudge::canGenerateCompetitionResults($comp))
            <x-add-card link="{{ route('comps.results.add', $comp) }}" text="Results" />
        @endif
    </div>
    <br>
    <h3>Masters</h3>
    <p class="mb-4">Master result sheets are made up by combining multiple normal result sheets. These are good for
        calculating an overall from multiple League result sheets
    </p>

    <div class="grid-4">
        @foreach ($comp->getMasterSchemas as $schema)
            <a href="{{ route('comps.results.view-schema', ['comp' => $comp, 'schema' => $schema]) }}" class="se-btn">
                <p class="text-lg font-semibold">{{ $schema->name }}</p>


            </a>
        @endforeach
        @if (DigitalJudge::canGenerateCompetitionResults($comp))
            <x-add-card link="{{ route('comps.results.master.add', $comp) }}" text="Results" />
        @endif
    </div>

    <br>

    <hr class="spacer">
    <br>
    <div class="grid-4">
        <div>

            @if ($comp->getResultSchemas->count() == 0)
                <div class="alert-box md:row-start-2 md:col-start-1 md:col-span-2 ">

                    <h1>Options unavailable</h1>
                    <p>Publishing and additional settings are unavailable until atleast one result sheet has been generated!
                    </p>

                </div>
            @else
                <h3>Publicize Results</h3>

                <div>
                    @if ($comp->areResultsPublic())
                        <a href="{{ route('comps.results.publishToggle', $comp) }}"
                            class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                            <p class="font-archivo">Unpublish Results</p>


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>





                        </a>
                        <a href="{{ route('landing.competition.results', $comp->resultsSlug()) }}" target="_blank"
                            class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                            <p class="font-archivo">View Results</p>


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>






                        </a>
                    @else
                        <a href="{{ route('comps.results.publishToggle', $comp) }}"
                            class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                            <p class="font-archivo">Publish Results</p>


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>






                        </a>
                    @endif

                </div>

                @if (!$comp->isLeague)
                    <div class="alert-box mt-3">

                        <h1>Private Event</h1>
                        <p>These results will <strong>not</strong> show on the public results page, and can only be accessed
                            via
                            the link below!
                        </p>

                    </div>
                @endif


        </div>
        <div>
            <h3>Result Settings</h3>

            <div>
                @if ($comp->areResultsProvisional())
                    <a href="{{ route('comps.results.provToggle', $comp) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Finalise Results</p>
                            <small class=" ml-5 text-gray-500">Removes provisional text, and allows for CSV download</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.results.provToggle', $comp) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Unfinalise Results</p>
                            <small class=" ml-5 text-gray-500">Adds provisional text, and removes the ability to download
                                CSVs</small>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>


                    </a>
                @endif

            </div>



        </div>
        @endif
    </div>
@endsection
