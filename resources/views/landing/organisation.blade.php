{{-- An Organisation: its Championships, then its competitions (coming up, then results) --}}
@extends('landing.v2.layout')

@section('title', $org->name)

@section('content')
    <div class="v2-container pb-16">
        <div class="flex items-center gap-5 pt-12">
            @if ($org->getLogo())
                <img src="{{ $org->getLogo() }}" alt="" class="size-16 shrink-0 rounded-full">
            @endif
            <div>
                <h1 class="v2-page-title">{{ $org->name }}</h1>
                <p class="v2-page-lead">Organisation</p>
            </div>
        </div>

        @if ($championships->isNotEmpty())
            <section class="v2-section" aria-labelledby="org-championships">
                <div class="v2-section-head">
                    <h2 id="org-championships">Championships</h2>
                </div>
                <ul class="v2-list">
                    @foreach ($championships as $championship)
                        <li class="v2-row">
                            <a href="{{ route('landing.championship', $championship->slug()) }}"
                                class="v2-list-name v2-row-link">{{ $championship->name }}</a>
                            @if ($championship->isCurrent())
                                <span class="v2-badge is-live">Current</span>
                            @endif
                            <span class="v2-list-detail">{{ $championship->start_date->format('j M Y') }} &ndash;
                                {{ $championship->end_date->format('j M Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="v2-section" aria-labelledby="org-ahead">
            <div class="v2-section-head">
                <h2 id="org-ahead">Coming up</h2>
            </div>
            <x-v2.fixtures :live="$live" :upcoming="$upcoming" :types="true"
                empty="Nothing coming up. {{ $org->name }}'s next competitions appear here once they're set up." />
        </section>

        @if ($past->isNotEmpty())
            <section class="v2-section" aria-labelledby="org-past">
                <div class="v2-section-head">
                    <h2 id="org-past">Results</h2>
                </div>
                <x-v2.fixtures :past="$past" :today="false" :types="true" />
            </section>
        @endif
    </div>
@endsection
