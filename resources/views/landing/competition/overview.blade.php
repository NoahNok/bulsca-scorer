{{-- A competition's Info tab: when and where, and its Events --}}
@extends('landing.competition.layout')

@section('title', $comp->name)

@section('comp-content')
    <div class="grid gap-10 md:grid-cols-[2fr_1fr]">
        <section aria-labelledby="info-events">
            <div class="v2-section-head">
                <h2 id="info-events">Events</h2>
            </div>
            @php
                $events = $comp->getSpeedEvents->map(fn($e) => ['name' => $e->getName(), 'serc' => false])
                    ->concat($comp->getSERCs->map(fn($e) => ['name' => $e->getName(), 'serc' => true]));
            @endphp
            @if ($events->isEmpty())
                <p class="v2-empty">The Events appear here once the Organisers add them.</p>
            @else
                <ul class="v2-list">
                    @foreach ($events as $event)
                        <li>
                            <span class="v2-list-name">{{ $event['name'] }}</span>
                            @if ($event['serc'])
                                <span class="v2-badge">SERC</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section aria-labelledby="info-details">
            <div class="v2-section-head">
                <h2 id="info-details">Details</h2>
            </div>
            <dl class="v2-details">
                <dt>When</dt>
                <dd>{{ $comp->when->format('l j F Y') }}</dd>
                <dt>Where</dt>
                <dd>{{ $comp->where }}
                    <a class="v2-link mt-1 block" target="_blank" rel="noopener"
                        href="https://www.google.com/maps/search/?{{ http_build_query(['api' => 1, 'query' => $comp->where]) }}">Open
                        in Google Maps &nearr;</a>
                </dd>
                @if ($comp->getOrganisation)
                    <dt>Organisation</dt>
                    <dd><a href="{{ route('landing.organisation', $comp->getOrganisation->name) }}"
                            class="v2-link">{{ $comp->getOrganisation->name }}</a></dd>
                @endif
            </dl>
        </section>
    </div>
@endsection
