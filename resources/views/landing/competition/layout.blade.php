{{--
    Shell for a competition's public pages: its name, date, venue, Championship and Organisation logo, the page
    tabs (Info, Heats & Draws, Results; the SERC breakdown counts as Results), Follow live when it's live today,
    and the Organisers' status message. Pages fill @section('comp-content').
--}}
@extends('landing.v2.layout')

@php
    $route = Route::currentRouteName();
    $tabs = [
        ['Info', route('landing.competition', $comp->getSlug()), $route === 'landing.competition'],
        ['Heats & Draws', route('landing.competition.heats-draws', $comp->getSlug()), $route === 'landing.competition.heats-draws'],
        ['Results', route('landing.competition.results', $comp->getSlug()), str_starts_with($route, 'landing.competition.results')],
    ];
    $championship = $comp->championship;
    $org = $comp->getOrganisation;
@endphp

@section('content')
    <div class="v2-container pb-16">
        <header class="v2-comp-head">
            <div class="min-w-0">
                <h1 class="v2-comp-title">{{ $comp->name }}</h1>
                <p class="v2-comp-meta">
                    <span class="v2-comp-date">{{ $comp->when->format('l j F') }}</span>
                    @if (!$comp->when->isCurrentYear())
                        {{ $comp->when->format('Y') }}
                    @endif
                    &middot; {{ $comp->where }}
                    @if ($championship)
                        &middot; <a href="{{ route('landing.championship', $championship->slug()) }}"
                            class="v2-link">{{ $championship->name }}</a>
                        @if ($comp->type && $comp->type !== 'STANDALONE')
                            <span class="v2-badge">{{ ucfirst(strtolower($comp->type)) }}</span>
                        @endif
                    @endif
                </p>
            </div>
            @if ($org)
                <a href="{{ route('landing.organisation', $org->name) }}" class="v2-comp-org" title="{{ $org->name }}">
                    @if ($org->getLogo())
                        <img src="{{ $org->getLogo() }}" alt="{{ $org->name }}">
                    @else
                        <span class="v2-link">{{ $org->name }}</span>
                    @endif
                </a>
            @endif
        </header>

        <div class="v2-comp-tabs">
            <nav class="v2-pagetabs" aria-label="Competition">
                @foreach ($tabs as [$label, $url, $current])
                    <a href="{{ $url }}"{!! $current ? ' aria-current="page"' : '' !!}>{{ $label }}</a>
                @endforeach
            </nav>
            @if ($comp->isLiveToday())
                <a href="{{ route('live', ['comp' => $comp->id]) }}" class="v2-btn v2-btn-primary v2-btn-sm">
                    <span class="v2-dot is-live" aria-hidden="true"></span> Follow live</a>
            @endif
        </div>

        @if ($comp->status_message)
            <div class="v2-alert is-info mt-6" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 11v5M12 8h.01" />
                </svg>
                <div class="v2-comp-status">{!! $comp->status_message !!}</div>
            </div>
        @endif

        <div class="mt-8">
            @yield('comp-content')
        </div>
    </div>
@endsection
