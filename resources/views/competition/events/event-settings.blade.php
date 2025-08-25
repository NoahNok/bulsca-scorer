@extends('layouts.competition')

@section('title')
    {{ $event->getName() }}
@endsection

@section('breadcrumbs')


@section('content')

    <div class="flex justify-between">
        <h2 class="mb-0">Scoring Settings - {{ $event->getName() }}</h2>

    </div>

    <div class="grid-3 ">
        <x-scoring-schema-editor :route="$route" :schema="$event->scoringSchema" :org="$comp->getOrganisation" type="event" />

        <div class="flex flex-col space-y-4">

            <div class="sticky top-4">



                <a href="{{ $returnRoute }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Back to event</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>




            </div>


        </div>


    </div>



@endsection
