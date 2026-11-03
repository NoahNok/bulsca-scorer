{{-- Every public competition (today's, upcoming, then past ones a page at a time) and every Organisation --}}
@extends('landing.v2.layout')

@section('title', 'Explore')

@section('content')
    <div class="v2-container pb-16">
        <div class="pt-12">
            <h1 class="v2-page-title">Explore</h1>
            <p class="v2-page-lead">Everything in one place.</p>
            <div class="mt-6">
                <x-v2.search />
            </div>
        </div>

        {{-- Past ones page on their own, so the first page's Today and upcoming rows stay put --}}
        @if ($past->onFirstPage())
            <section class="v2-section" aria-labelledby="explore-ahead">
                <div class="v2-section-head">
                    <h2 id="explore-ahead">Coming up</h2>
                </div>
                <x-v2.fixtures :live="$live" :upcoming="$upcoming"
                    empty="Nothing coming up yet. Organisers add competitions here as soon as they're set up." />
            </section>
        @endif

        <section class="v2-section" aria-labelledby="explore-past">
            <div class="v2-section-head">
                <h2 id="explore-past">Results</h2>
            </div>
            <x-v2.fixtures :past="collect($past->items())" :today="false" empty="No past competitions yet." />

            @if ($past->hasPages())
                <nav class="v2-pager" aria-label="Past competitions">
                    @if ($past->previousPageUrl())
                        <a href="{{ $past->previousPageUrl() }}" class="v2-btn v2-btn-sm">&larr; Newer</a>
                    @else
                        <span></span>
                    @endif
                    @if ($past->nextPageUrl())
                        <a href="{{ $past->nextPageUrl() }}" class="v2-btn v2-btn-sm">Older &rarr;</a>
                    @endif
                </nav>
            @endif
        </section>

        @if ($past->onFirstPage())
            <section class="v2-section" aria-labelledby="explore-orgs">
                <div class="v2-section-head">
                    <h2 id="explore-orgs">Organisations</h2>
                </div>
                @if ($orgs->isEmpty())
                    <p class="v2-empty">No organisations yet.</p>
                @else
                    <ul class="v2-list v2-list-columns">
                        @foreach ($orgs as $org)
                            <li class="v2-row">
                                @if ($org->getLogo())
                                    <img src="{{ $org->getLogo() }}" alt="">
                                @endif
                                <a href="{{ route('landing.organisation', $org->name) }}"
                                    class="v2-list-name v2-row-link">{{ $org->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endif
    </div>
@endsection
