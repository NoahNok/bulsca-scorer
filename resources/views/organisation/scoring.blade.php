@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Scoring Schemas</h2>
    <p>Create and modify event scoring profiles for this organisation.</p>

    <div class="grid-3 mt-2">



        @foreach ($org->scoringSchemas as $schema)
            <a href="{{ route('orgs.scoring.edit', [$org, $schema]) }}"
                class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                <p class="font-archivo flex items-center">{{ $schema->name }}
                </p>



                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


            </a>
        @endforeach


        <x-add-card link="{{ route('orgs.scoring.create', $org->name) }}" />




    </div>

    <hr class="spacer my-6!">
    <h2 class="mb-0">Result Scoring Schemas</h2>
    <p>Create and modify result scoring schemas for this organisation.</p>

    <div class="grid-3 mt-2">



        @foreach ($org->resultSchemas as $schema)
            <a href="{{ route('orgs.scoring.result-schema.edit', [$org, $schema]) }}"
                class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                <p class="font-archivo flex items-center">{{ $schema->name }}
                </p>



                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


            </a>
        @endforeach


        <x-add-card link="{{ route('orgs.scoring.result-schema.create', $org->name) }}" />




    </div>
@endsection
