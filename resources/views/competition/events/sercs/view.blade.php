@extends('layout')

@section('title')
    {{ $serc->name }} | {{ $comp->name }}
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
        <a href="{{ route('comps.view.events', $comp) }}">Events</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">{{ $serc->name }}</a>
    </div>
@endsection

@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4" x-data="{
            search: '',
        }">

            <div class="flex justify-between">
                <h2 class="mb-0">{{ $serc->name }}</h2>
                <a href="{{ route('comps.view.events.sercs.edit', [$comp, $serc]) }}" class="btn">Edit SERC Setup</a>
            </div>

            <h4>Marked Teams</h4>
            <div class="  relative w-full overflow-x-auto  ">
                <div class="form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams" x-model="search">
                </div>

                <br>
                @include('competition.events.sercs.table_templates.' . $comp->scoring_type)
            </div>

            <h4>All teams</h4>
            @if ($comp->needsToRegenerateSERCDraw())
                <div class="alert-box alert-warning">
                    <h1>Missing SERC Order</h1>
                    <p>The teams below are showing in the default entered order, as no SERC order has been generated
                        <strong>OR</strong> a new SERC order needs to be <a href="{{ route('comps.view.heats', $comp) }}"
                            class="link">generated</a>.
                        <br>
                        <br>
                        If you are using an external order please ignore this message.
                    </p>
                </div>
            @endif
            <div class="  relative w-full  ">
                <div class="form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams" x-model="search">
                </div>

                <br>

                <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="py-3 px-6 text-left">
                                Team
                            </th>

                            <th scope="col" class="py-3 px-6">
                                Results
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($serc->getTeams() as $team)
                            <tr class="bg-white border-b text-right " x-data="{ name: `{{ $team->getFullname() }}` }"
                                x-show="name.toLowerCase().includes(search.toLowerCase())">
                                <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $loop->index + 1 }}: {{ $team->getFullname() }}
                                </th>

                                <td class="py-4 px-6">
                                    <a href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $team]) }}"
                                        class="btn btn-primary btn-thin">
                                        Edit
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr class="bg-white border-b text-right ">
                                <th colspan="100" scope="row"
                                    class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                                    None
                                </th>
                            </tr>
                        @endforelse



                    </tbody>
                </table>
            </div>

        </div>

        <div class="flex flex-col space-y-4">
            <h2 class="mb-0">Options</h2>
            <div class="card space-y-4">
                <div class="flex justify-between items-center">
                    <strong>Delete SERC</strong>
                    <form action="{{ route('comps.view.events.sercs.delete', [$comp, $serc]) }}"
                        onsubmit="return confirm('Are you sure you want to delete this SERC!')" method="post">
                        <input type="hidden" name="sid" value="{{ $serc->id }}">
                        {{ method_field('DELETE') }}
                        @csrf
                        <button class="btn btn-danger">Delete SERC</button>
                    </form>
                </div>





                <div class="flex justify-between items-center">
                    <strong>Rough Judge Sheets</strong>

                    <a href="{{ route('comps.view.printables.serc-sheets', [$comp, $serc]) }}"
                        class="btn btn-purple">Print</a>

                </div>
                @if ($serc->viewable)
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Hide from Results</strong>
                            <small>This will make this SERC hidden on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.sercs.hide', [$comp, $serc]) }}" class="btn btn-danger">Hide
                                SERC</a>
                        </div>

                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Unhide from Results</strong>
                            <small>This will make this SERC visible on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.sercs.hide', [$comp, $serc]) }}" class="btn ">Unhide
                                SERC</a>

                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <strong>SERC Image</strong>
                        <small>Upload an image to display at the top of SERC marking sheets and digital judge</small>
                    </div>

                    <form action="{{ route('comps.view.sercs.image', [$comp, $serc]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-input imb-0 ">
                            <input type="file" name="image" id="" onchange="form.submit()">
                        </div>
                    </form>

                </div>

                @if ($serc->image)
                    <div class="flex justify-between items-center">
                        <img src="{{ asset('storage/' . $serc->image) }}" alt="SERC Image" class=" max-w-[25%] ">
                        <a href="{{ route('comps.view.sercs.image.remove', [$comp, $serc]) }}"
                            class="btn btn-danger">Remove</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
