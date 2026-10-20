{{-- The v2 home page: the pool marquee and search, one sheet of fixtures and results split at today, then what Scoring.Events is. --}}
@extends('landing.v2.layout')

@section('v2-styles')
    <style>
        .f-hero {
            position: relative;
            /* Sits above the sheet so the search results can drop over it */
            z-index: 1;
            /* Clip the marquee sideways only; hidden would cut off the search results */
            overflow-x: clip;
            padding: 3.5rem 0 4.5rem;
            color: #f9fafb;
        }

        .f-hero-bg {
            position: absolute;
            inset: 0;
            z-index: -1;
            background: linear-gradient(180deg, rgb(17 24 39 / 0.55), rgb(17 24 39 / 0.85)), url('{{ asset('pool.jpg') }}') center / cover;
        }

        /* Dark: follows the OS unless the theme toggle forces light; forced dark matches too */
        @media (prefers-color-scheme: dark) {
            html:not([data-theme="light"]) {
                .f-hero-bg {
                    background: linear-gradient(180deg, rgb(3 7 18 / 0.7), rgb(3 7 18 / 1)), url('{{ asset('pool.jpg') }}') center / cover;
                }

            }
        }

        html[data-theme="dark"] {
            .f-hero-bg {
                background: linear-gradient(180deg, rgb(3 7 18 / 0.7), rgb(3 7 18 / 1)), url('{{ asset('pool.jpg') }}') center / cover;
            }

        }

        .f-marquee {
            position: relative;
            height: 8rem;
            white-space: nowrap;
        }

        .f-marquee>div {
            position: absolute;
            top: 0;
            right: 100%;
            width: 100%;
            display: flex;
            gap: 9rem;
        }

        .f-word {
            font-size: clamp(2.5rem, 6vw, 3.75rem);
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-transform: uppercase;
            color: #fff;
        }

        .f-word span {
            display: block;
            font-size: 1.2em;
            color: #00d5be;
        }

        .f-word.is-flip {
            color: #00d5be;
        }

        .f-word.is-flip span {
            color: #fff;
        }

        .f-hero-inner {
            max-width: 76rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .f-hero-search {
            max-width: 40rem;
            margin: 3.5rem auto 0;
        }

        /* The search box sits on the photo, so give it the light surface whatever the theme */
        .f-hero .se-form-input input {
            background: rgb(255 255 255 / 0.95);
            color: #111827;
            border-color: transparent;
        }

        .f-wrap {
            max-width: 76rem;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .f-section {
            padding: 3.5rem 0 1rem;
        }

        .f-section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .f-section-head h2 {
            font-size: 1.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Fixtures and results: one sheet in date order, split by a teal line at today */
        .f-num {
            font-family: 'Anton', 'Archivo', sans-serif;
            font-weight: 400;
            letter-spacing: 0.01em;
            line-height: 1;
        }

        .f-sheet {
            width: 100%;
            border-collapse: collapse;
        }

        .f-sheet th {
            text-align: left;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--v2-muted);
            padding: 0 1rem 0.75rem 0;
        }

        .f-sheet td {
            padding: 1rem 1rem 1rem 0;
            border-top: 1px solid var(--v2-border);
            vertical-align: middle;
        }

        /* Inset the rows a little, so a hovered row's shading doesn't sit flush with its text */
        .f-sheet th:first-child,
        .f-sheet td:first-child {
            padding-left: 0.75rem;
        }

        .f-sheet td:last-child {
            padding-right: 0.75rem;
            text-align: right;
        }

        .f-sheet .f-today td {
            padding-left: 0;
            padding-right: 0;
        }

        .f-date {
            font-size: 1.5rem;
            white-space: nowrap;
            width: 6rem;
        }

        .f-name {
            font-weight: 700;
        }

        .f-where {
            color: var(--v2-muted);
        }

        .f-where-sm {
            display: none;
            font-size: 0.875rem;
            color: var(--v2-muted);
        }

        .f-sheet img {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
        }

        /* Live and upcoming rows say when, in words, where results show a badge */
        .f-when {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8125rem;
            font-weight: 600;
            white-space: nowrap;
            color: var(--v2-muted);
        }

        .f-when.is-live {
            color: var(--v2-teal-text);
        }

        /* Results are history, so they sit back a little */
        .f-past .f-date,
        .f-past .f-name {
            color: var(--v2-muted);
        }

        /* Each row is one link (.v2-row). Results keep their faded name and date; only upcoming names turn teal */
        .f-ahead:hover .f-name,
        .f-ahead:focus-within .f-name {
            color: var(--v2-teal-text);
        }

        /* Today's row: Details covers the row, Follow live sits above it */
        .f-live-actions {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
        }

        /* Today: a teal line, with today's competitions on it */
        .f-today td {
            border-top: 0;
            padding: 0.25rem 0;
        }

        /* The line sits at the label's baseline, so the gap down to the live row is the same under the label and under the line */
        .f-rope {
            display: flex;
            align-items: baseline;
            gap: 1rem;
            font-weight: 700;
            white-space: nowrap;
        }

        /* Drop the label onto the line: about half its cap height, so the line runs through its middle */
        .f-rope-label {
            transform: translateY(0.35em);
        }

        .f-rope::after {
            content: '';
            flex: 1;
            height: 2px;
            background: var(--v2-teal);
        }

        /* A touch less on top: the Today row above already adds its own spacing */
        .f-live td {
            border-top: 0;
            padding-top: 1rem;
            padding-bottom: 1.25rem;
        }

        .f-live .f-name {
            font-size: 1.375rem;
        }

        .f-today+.f-ahead td,
        .f-live+.f-today td {
            border-top: 0;
        }

        @media (max-width: 640px) {
            .f-hide-sm {
                display: none;
            }

            .f-where-sm {
                display: block;
            }

            .f-date {
                width: 4.5rem;
                font-size: 1.25rem;
            }

            .f-live .f-name {
                font-size: 1.125rem;
            }
        }

        .f-sheet .v2-btn {
            white-space: nowrap;
        }

        .f-empty {
            padding: 2rem;
            border: 1px dashed var(--v2-border);
            border-radius: 1rem;
            color: var(--v2-muted);
        }

        /* What it is */
        .f-about {
            display: grid;
            gap: 2.5rem 4rem;
            padding: 4rem 0;
        }

        @media (min-width: 900px) {
            .f-about {
                grid-template-columns: 5fr 6fr;
            }
        }

        .f-about-statement {
            font-size: clamp(2rem, 4.5vw, 3.25rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.015em;
            text-transform: uppercase;
        }

        .f-about-list>div {
            padding: 1.25rem 0;
        }

        .f-about-list>div+div {
            border-top: 1px solid var(--v2-border);
        }

        .f-about-list h3 {
            font-size: 1.125rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .f-about-list p {
            color: var(--v2-muted);
            line-height: 1.6;
        }

        .f-about-list .v2-link {
            display: inline-block;
            margin-top: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <section class="f-hero">
        <div class="f-hero-bg" aria-hidden="true"></div>

        <h1 class="sr-only">Scoring.Events</h1>
        <div class="f-marquee" aria-hidden="true">
            <div class="animate-marquee">
                @for ($i = 0; $i < 14; $i++)
                    <p @class(['f-word', 'is-flip' => $i % 2])>Scoring.<span>Events</span></p>
                @endfor
            </div>
        </div>

        <div class="f-hero-inner ">
            <div class="f-hero-search">
                <x-search-all />
            </div>
        </div>
    </section>

    <div class="f-wrap">
        <section class="f-section" aria-labelledby="f-sheet">
            <div class="f-section-head">
                <h2 id="f-sheet">Fixtures and results</h2>
                <a href="{{ route('explore') }}" class="v2-link">All competitions</a>
            </div>

            @php
                // Oldest first, so the sheet reads down through the season to today and on
                $past = $recent->reverse();
            @endphp

            @if ($past->isEmpty() && $live->isEmpty() && $upcoming->isEmpty())
                <p class="f-empty">No competitions yet. Organisers add them here as soon as they're set up.</p>
            @else
                <table class="f-sheet">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Competition</th>
                            <th class="f-hide-sm">Venue</th>
                            <th class="f-hide-sm"><span class="sr-only">Organisation</span></th>
                            <th class="f-hide-sm">Status</th>
                            <th><span class="sr-only">Link</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($past as $comp)
                            <tr class="f-past v2-row">
                                <td class="f-date f-num">{{ $comp->when->format('j M') }}</td>
                                <td>
                                    <span class="f-name">{{ $comp->name }}</span>
                                    <span class="f-where-sm">{{ $comp->where }}</span>
                                </td>
                                <td class="f-where f-hide-sm">{{ $comp->where }}</td>
                                <td class="f-hide-sm">
                                    @if ($comp->getOrganisation?->getLogo())
                                        <img src="{{ $comp->getOrganisation->getLogo() }}"
                                            alt="{{ $comp->getOrganisation->name }}">
                                    @endif
                                </td>
                                <td class="f-hide-sm">
                                    @if ($comp->areResultsProvisional())
                                        <span class="v2-badge is-provisional">Provisional</span>
                                    @else
                                        <span class="v2-badge">Final</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('landing.competition.results', $comp->getSlug()) }}"
                                        class="v2-link v2-row-link">Results<span class="sr-only"> for
                                            {{ $comp->name }}</span></a></td>
                            </tr>
                        @endforeach

                        <tr class="f-today">
                            <td colspan="6">
                                <span class="f-rope"><span class="f-rope-label">Today,
                                        {{ today()->format('D j M') }}</span></span>
                            </td>
                        </tr>

                        @foreach ($live as $comp)
                            <tr class="f-live v2-row">
                                {{-- The Today line above already gives the date, so the name takes its column --}}
                                <td colspan="2">
                                    <span class="f-name">{{ $comp->name }}</span>
                                    <span class="f-where-sm">{{ $comp->where }}</span>
                                </td>
                                <td class="f-where f-hide-sm">{{ $comp->where }}</td>
                                <td class="f-hide-sm">
                                    @if ($comp->getOrganisation?->getLogo())
                                        <img src="{{ $comp->getOrganisation->getLogo() }}"
                                            alt="{{ $comp->getOrganisation->name }}">
                                    @endif
                                </td>
                                <td class="f-hide-sm">
                                    <span class="f-when is-live"><span class="v2-dot is-live" aria-hidden="true"></span>
                                        Live now</span>
                                </td>
                                <td>
                                    <div class="f-live-actions">
                                        <a href="{{ route('landing.competition', $comp->getSlug()) }}"
                                            class="v2-link v2-row-link"><span class="f-hide-sm"
                                                aria-hidden="true">Details</span><span class="sr-only">Details for
                                                {{ $comp->name }}</span></a>
                                        @if ($comp->canBeViewedLive())
                                            <a href="{{ route('live', ['comp' => $comp->id]) }}"
                                                class="v2-btn v2-btn-primary v2-over-row">Follow live</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if ($live->isNotEmpty())
                            <tr class="f-today">
                                <td colspan="6"><span class="f-rope" aria-hidden="true"></span></td>
                            </tr>
                        @endif

                        @foreach ($upcoming as $comp)
                            @php
                                $days = (int) today()->diffInDays($comp->when);
                            @endphp
                            <tr class="f-ahead v2-row">
                                <td class="f-date f-num">{{ $comp->when->format('j M') }}</td>
                                <td>
                                    <span class="f-name">{{ $comp->name }}</span>
                                    <span class="f-where-sm">{{ $comp->where }}</span>
                                </td>
                                <td class="f-where f-hide-sm">{{ $comp->where }}</td>
                                <td class="f-hide-sm">
                                    @if ($comp->getOrganisation?->getLogo())
                                        <img src="{{ $comp->getOrganisation->getLogo() }}"
                                            alt="{{ $comp->getOrganisation->name }}">
                                    @endif
                                </td>
                                <td class="f-hide-sm">
                                    <span class="f-when">{{ $days == 1 ? 'Tomorrow' : "In $days days" }}</span>
                                </td>
                                <td><a href="{{ route('landing.competition', $comp->getSlug()) }}"
                                        class="v2-link v2-row-link">Details<span class="sr-only"> for
                                            {{ $comp->name }}</span></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="f-about" aria-labelledby="f-about">
            <h2 id="f-about" class="f-about-statement">One scoring system for the whole pool</h2>
            <div class="f-about-list">
                <div>
                    <h3>Organisers</h3>
                    <p>Set up SERCs and speed events, take entries, generate heats and draws, and publish results as soon
                        as they're in. Give other organisers just the access they need, and group competitions under your
                        organisation.</p>
                    @guest
                        <a href="{{ route('register') }}" class="v2-link">Create an account</a>
                    @endguest
                </div>
                <div>
                    <h3>Competitors</h3>
                    <p>Follow the day live, see results the moment they're released, and use WhatIf to see how a
                        different time or mark would have changed the standings.</p>
                </div>
                <div>
                    <h3>Officials</h3>
                    <p>Judge, time and referee from your phone with Digital Judge. Submit DQs and penalties poolside and
                        get the referee's announcements as they happen.</p>
                    <a href="{{ route('dj.home') }}" class="v2-link">Open Digital Judge</a>
                </div>
            </div>
        </section>

        @include('landing.v2.partials.kit')
    </div>
@endsection
