@extends('layouts.competition')

@section('title')
    {{ $serc->name }}
@endsection



@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-2" x-data="{
            search: '',
        }">

            <div class="flex justify-between">
                <h2 class="mb-0">{{ $serc->name }}</h2>
                <a href="{{ route('comps.events.sercs.edit', [$comp, $serc]) }}" class="se-btn">Edit SERC Setup</a>
            </div>

            <h4>Marked Teams</h4>
            <div class="  relative w-full overflow-x-auto  ">
                @if ($serc->scoringSchema)
                    <div class="se-form-input imb-0 ">
                        <input type="text" table-search placeholder="Search teams" x-model="search">
                    </div>

                    <br>

                    <div class="  tabbed-bar mt-2  ">

                        <a href="{{ url()->current() }}" class="@if (!$activeLeague) active @endif">All</a>
                        @foreach ($comp->getLeagues as $league)
                            <a href="?league={{ $league->id }}"
                                class="@if ($activeLeague?->id == $league->id) active @endif">{{ $league->name }}</a>
                        @endforeach

                    </div>
                    <div class="se-table">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">
                                        Team
                                    </th>
                                    <th scope="col">
                                        DQ
                                    </th>
                                    <th scope="col">
                                        Raw Mark
                                    </th>
                                    <th scope="col">
                                        Points
                                    </th>
                                    <th scope="col">
                                        Position
                                    </th>
                                    <th scope="col">
                                        Results
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($eventResults as $result)
                                    <tr x-data="{ name: `{{ $result->entity->getName() }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                        <th scope="row">
                                            {{ $result->entity->getName() }}
                                        </th>
                                        <td>
                                            {{ $result->getDisqualificationsString() ?: '-' }}
                                        </td>
                                        <td>

                                            {{ $result->resolvedResult }}
                                        </td>
                                        <td>
                                            @php
                                                $res = round($result->points, 1);
                                            @endphp
                                            {!! $result->isDisqualified() ? "<s>{$res}</s> DQ" : $res !!}

                                        </td>
                                        <td>
                                            {{ $result->position }}
                                        </td>
                                        <td>
                                            <a href="{{ route('comps.events.sercs.editResults', [$comp, $serc, $result->entity->id]) }}"
                                                class="se-btn text-black">
                                                Edit
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr class="empty ">
                                        <th colspan="100" scope="row">
                                            None
                                        </th>
                                    </tr>
                                @endforelse



                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert-box ">
                        <h1>No Scoring Setup</h1>
                        <p>You have not setup any scoring rules for this SERC, thus no results could be generated.
                            <br>
                            Please go to the <a href="{{ route('comps.events.sercs.scoring-settings', [$comp, $serc]) }}"
                                class="link">scoring settings</a> page to set up scoring for this SERC.
                        </p>
                    </div>
                @endif
            </div>

            <h4>All teams</h4>



            @php
                $sercDraw = $serc->getDraw;

            @endphp

            @if ($sercDraw->empty())
                <div class="alert-box">
                    <h1>No SERC Draw</h1>
                    <p>No SERC draw has been made, the items below are not ordered.
                    </p>
                </div>
            @endif

            <div class="  relative w-full  ">
                <div class="se-form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams" x-model="search">
                </div>

                <br>

                <div class="se-table">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">
                                    Team
                                </th>

                                <th scope="col">
                                    Results
                                </th>

                            </tr>
                        </thead>
                        <tbody>

                            @if (!$sercDraw->empty())
                                @forelse ($sercDraw as $draw)
                                    <tr x-data="{ name: `{{ $draw->entity->getName() }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                        <th scope="row">
                                            {{ $draw->draw }}. {{ $draw->entity->getName() }}
                                        </th>

                                        <td>
                                            <a href="{{ route('comps.events.sercs.editResults', [$comp, $serc, $draw->entity]) }}"
                                                class="se-btn text-black0">
                                                Edit
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr class="empty ">
                                        <th colspan="100" scope="row">
                                            None
                                        </th>
                                    </tr>
                                @endforelse
                            @else
                                @forelse ($serc->getScorableEntities() as $entity)
                                    <tr x-data="{ name: `{{ $entity->getName() }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                        <th scope="row">
                                            {{ $entity->getName() }}
                                        </th>

                                        <td>
                                            <a href="{{ route('comps.events.sercs.editResults', [$comp, $serc, $entity]) }}"
                                                class="se-btn text-black0">
                                                Edit
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr class="empty ">
                                        <th colspan="100" scope="row">
                                            None
                                        </th>
                                    </tr>
                                @endforelse
                            @endif



                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="flex flex-col space-y-4">


            <div class="sticky top-4">
                <form action="{{ route('comps.view.events.sercs.delete', [$comp, $serc]) }}"
                    onsubmit="return confirm('Are you sure you want to delete this SERC!')" method="post">
                    <input type="hidden" name="sid" value="{{ $serc->id }}">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1 w-full">
                        <p class="font-archivo">Delete SERC</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>





                    </button>
                </form>

                <a href="{{ route('comps.events.sercs.scoring-settings', [$comp, $serc]) }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Scoring Settings</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>

                @if ($serc->viewable)
                    <a href="{{ route('comps.view.sercs.hide', [$comp, $serc]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Hide SERC from results</p>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.view.sercs.hide', [$comp, $serc]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Show SERC in results</p>


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

                <a href="{{ route('comps.printables.serc-sheets', [$comp, $serc]) }}" target="_blank"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Print rough mark sheets</p>


                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>



                </a>
            </div>


            <div class="card space-y-4" hidden>



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
