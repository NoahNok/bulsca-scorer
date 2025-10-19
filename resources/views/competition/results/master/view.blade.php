@extends('layouts.competition')

@section('title')
    {{ $schema->name }} | Master Sheet
@endsection

@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4 md:col-span-2">

            <div class="flex justify-between">
                <h2 class="mb-0">{{ $schema->name }} | Master Sheet</h2>

            </div>


            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                Name
                            </th>
                            <th scope="col">
                                Total
                            </th>
                            <th scope="col">
                                Position
                            </th>


                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($results as $result)
                            <tr>
                                <th scope="row">
                                    {{ $result->entity->getName($comp) }}
                                </th>
                                <td>
                                    {{ $result->total }}
                                </td>

                                <td>
                                    {{ $result->position }}
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



        </div>
        <div class="flex flex-col space-y-4">


            <div>
                <form action="{{ route('comps.results.master.delete', [$comp, $schema->id]) }}}"
                    onsubmit="return confirm('Are you sure you want to delete this result sheet?')" method="post">
                    <input type="hidden" name="sid" value="{{ $schema->id }}">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1 w-full">
                        <p class="font-archivo">Delete sheet</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>





                    </button>
                </form>

                <a href="{{ route('comps.results.view-schema-print-basic', $schema) }}" target="_blank"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <div class="font-archivo">
                        <p class="-mb-1">Print Places</p>
                        <small class=" ml-5 text-gray-500">Prints final places and points only</small>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>

                <a href="{{ route('comps.results.view-schema-print', $schema) }}" target="_blank"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <div class="font-archivo">
                        <p class="-mb-1">Print Detailed</p>
                        <small class=" ml-5 text-gray-500">Prints all event places and points along with final totals and
                            places</small>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>

                @if ($schema->viewable)
                    <a href="{{ route('comps.results.master.hide', [$comp, $schema->id]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Hide sheet from results</p>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.results.master.hide', [$comp, $schema->id]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Show sheet in results</p>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </a>
                @endif


            </div>





        </div>



        <div>
            <h3>Weightings</h3>
            <ul>
                @foreach ($schema->getSheets() as $sheet)
                    <li><strong>{{ $sheet->name }}</strong>: {{ $sheet->weight }}</li>
                @endforeach
            </ul>


        </div>



        <div class="col-span-full">




        </div>
    @endsection
