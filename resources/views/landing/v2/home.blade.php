{{-- The v2 home page: the pool marquee and search, what's coming up, recent results, then what Scoring.Events is. --}}
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

        /* Two identical runs of words: sliding one run's width brings the second exactly where the first
                           started, so the loop never shows a seam or runs out */
        .f-marquee-track {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            width: max-content;
            animation: f-marquee 240s linear infinite;
        }

        .f-marquee-track>.f-word {
            flex-shrink: 0;
            padding-right: 9rem;
        }

        @keyframes f-marquee {
            to {
                transform: translateX(-50%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .f-marquee-track {
                animation: none;
            }
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
        .f-hero .v2-search-input {
            background: rgb(255 255 255 / 0.95);
            color: #111827;
            border-color: transparent;
        }

        .f-wrap {
            max-width: 76rem;
            margin: 0 auto;
            padding: 0 1rem;
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
            <div class="f-marquee-track">
                {{-- 8 words a run (even, so the flips line up across the seam), wider than any screen --}}
                @for ($i = 0; $i < 16; $i++)
                    <p @class(['f-word', 'is-flip' => $i % 2])>Scoring.<span>Events</span></p>
                @endfor
            </div>
        </div>

        <div class="f-hero-inner ">
            <div class="f-hero-search">
                <x-v2.search />
            </div>
        </div>
    </section>

    <div class="f-wrap">
        <section class="v2-section" aria-labelledby="f-ahead">
            <div class="v2-section-head">
                <h2 id="f-ahead">Coming up</h2>
                <a href="{{ route('explore') }}" class="v2-link">All competitions</a>
            </div>
            <x-v2.fixtures :live="$live" :upcoming="$upcoming"
                empty="Nothing coming up yet. Organisers add competitions here as soon as they're set up." />
        </section>

        @if ($recent->isNotEmpty())
            <section class="v2-section" aria-labelledby="f-past">
                <div class="v2-section-head">
                    <h2 id="f-past">Results</h2>
                </div>
                <x-v2.fixtures :past="$recent" :today="false" />
            </section>
        @endif

        <section class="f-about" aria-labelledby="f-about">
            <h2 id="f-about" class="f-about-statement">One scoring system for the whole pool</h2>
            <div class="f-about-list">
                <div>
                    <h3>Organisers</h3>
                    <p>Set up SERCs and speed events, take entries, generate heats and draws, and publish results as soon
                        as they're in. Invite collaborators, and group competitions under your
                        organisation.</p>
                    <a href="{{ route('register') }}" class="v2-link">Create an account</a>
                </div>
                <div>
                    <h3>Competitors</h3>
                    <p>Follow live, see your results as soon as they are released, and use WhatIf to see how you could have
                        done.</p>
                </div>
                <div>
                    <h3>Officials</h3>
                    <p>Judge, time and mark events from your phone with Digital Judge. Submit DQs and penalties poolside in
                        real-time.</p>
                    <a href="{{ route('judge.login') }}" class="v2-link">Open Digital Judge</a>
                </div>
            </div>
        </section>

    </div>
@endsection
