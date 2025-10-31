@extends('layouts.landing')

@section('title', 'Explore')

@section('content')




    @isset($ongoing)
        <div class="se-card se-card-body se-card-hover cursor-default! mb-6"
            @click="window.location='{{ route('landing.competition', $ongoing->getSlug()) }}'">
            <div class="se-card-body">
                <h3 class="text-se animate-pulse">Live</h3>
                <h1>{{ $ongoing->name }}</h1>
                <p class="font-archivo text-xs! text-gray-700! uppercase -mb-1">{{ $ongoing->when->format('M jS Y') }}</p>

                <div class="mt-5">
                    {{-- <a href="{{ route('live', ['comp' => $ongoing->id]) }}" class="se-btn se-btn-outline-primary">Live</a> --}}

                    <a href="{{ route('landing.competition.heats-draws', $ongoing->getSlug()) }}"
                        class="se-btn se-btn-outline-primary">Heats &
                        Draws</a>
                    <a href="{{ route('landing.competition.results', $ongoing->getSlug()) }}"
                        class="se-btn @if (!$ongoing->public_results) se-btn-disabled @endif">
                        Results
                    </a>
                </div>
            </div>

        </div>
    @endisset

    <div>
        <x-search-all />
    </div>


    <h2>Competitions</h2>
    <div class="grid-4  mt-2">
        @foreach ($comps as $comp)
            <x-competition-card url="{{ route('landing.competition', $comp->getSlug()) }}" :comp="$comp"
                class=" "></x-competition-card>
        @endforeach

    </div>


    {{ $comps->appends(['orgs_page' => $orgs->currentPage()])->links() }}

    <br>
    <hr class="spacer mt-3!">
    <br>

    <h2>Organisations</h2>
    <div class="grid-4  mt-2">
        @foreach ($orgs as $org)
            <a href="{{ route('landing.organisation', $org) }}" class="se-card se-card-hover se-card-body ">
                <div class="flex items-center justify-between">
                    <h2>{{ $org->name }}</h2>
                    <img src="{{ $org->getLogo() }}" alt="" class="size-8 rounded-full">
                </div>
            </a>
        @endforeach

    </div>
    <br>

    {{ $orgs->appends(['page' => $comps->currentPage()])->links() }}
@endsection
