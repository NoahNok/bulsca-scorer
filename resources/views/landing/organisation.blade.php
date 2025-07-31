@extends('layouts.landing')

@section('title', $org->name)

@section('content')

    <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0 py-8 ">
        <h1 class="text-5xl! -mb-1 ">{{ $org->name }}</h1>
        <img src="{{ $org->getLogo() }}" alt="" class="size-16 rounded-full">

    </div>



    @isset($ongoing)
        <div class="se-card se-card-body se-card-hover cursor-default! mb-6">
            <div class="se-card-body">
                <h3 class="text-se animate-pulse">Live</h3>
                <h1>{{ $ongoing->name }}</h1>
                <p class="font-archivo text-xs! text-gray-700! uppercase -mb-1">{{ $ongoing->when->format('M jS Y') }}</p>

                <div class="mt-5">
                    <a href="{{ route('live', ['comp' => $ongoing->id]) }}" class="se-btn se-btn-outline-primary">Live</a>

                    <a href="{{ route('landing.competition.heats-draws', $ongoing->getSlug()) }}" class="se-btn">Heats &
                        Draws</a>
                    <a href="{{ route('landing.competition.results', $ongoing->getSlug()) }}"
                        class="se-btn @if (!$ongoing->public_results) se-btn-disabled @endif">
                        Results
                    </a>
                </div>
            </div>

        </div>
    @endisset


    <h2>Competitions</h2>



    <div class="grid-4  mt-2">
        @forelse ($org->getCompetitions as $comp)
            <x-competition-card url="{{ route('landing.competition', $comp->getSlug()) }}" :comp="$comp"
                :org="$org"></x-competition-card>
        @empty
            <div class="alert-box col-span-full">
                <h1>No Competitions</h1>
                <p>{{ $org->name }} doesn't have any competitions.</p>
            </div>
        @endforelse

    </div>
@endsection
