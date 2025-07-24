@extends('layouts.landing')

@section('title', $org->name)

@section('content')

    <div class="flex items-center py-8 space-x-4 z-20">
        <img src="{{ $org->getLogo() }}" alt="" class="size-16 rounded-full">
        <h1 class="text-5xl!  ">{{ $org->name }}</h1>
    </div>



    @isset($ongoing)
        <div class="se-card se-card-body se-card-hover cursor-default! mb-6">
            <div class="se-card-body">
                <h3 class="text-se">Live</h3>
                <h1>{{ $ongoing->name }}</h1>
                <p class="font-archivo text-xs! text-gray-700! uppercase -mb-1">{{ $ongoing->when->format('M jS Y') }}</p>

                <div class="mt-5">
                    <a href="{{ route('landing.competition', $ongoing->getSlug()) }}"
                        class="se-btn se-btn-outline-primary">Live</a>
                    <a href="{{ route('landing.competition.heats-draws', $ongoing->getSlug()) }}" class="se-btn">Heats &
                        Draws</a>
                    <a href="{{ route('landing.competition.results', $ongoing->getSlug()) }}" class="se-btn">
                        Results
                    </a>
                </div>
            </div>

        </div>
    @endisset


    <h2>Competitions</h2>



    <div class="grid-4 z-20 mt-2">
        @foreach ($org->getCompetitions as $comp)
            <x-competition-card url="{{ route('landing.competition', $comp->getSlug()) }}" :comp="$comp"
                :org="$org"></x-competition-card>
        @endforeach

    </div>
@endsection
