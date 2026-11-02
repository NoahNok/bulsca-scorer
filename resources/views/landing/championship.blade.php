{{-- A Championship: its Organisation and dates, then its competitions in date order --}}
@extends('landing.v2.layout')

@section('title', $championship->name)

@section('content')
    <div class="v2-container pb-16">
        <div class="flex items-center gap-5 pt-12">
            @if ($championship->organisation->getLogo())
                <a href="{{ route('landing.organisation', $championship->organisation->name) }}" class="shrink-0">
                    <img src="{{ $championship->organisation->getLogo() }}" alt="{{ $championship->organisation->name }}"
                        class="size-16 rounded-full">
                </a>
            @endif
            <div>
                <h1 class="v2-page-title">{{ $championship->name }}</h1>
                <p class="v2-page-lead">
                    <a href="{{ route('landing.organisation', $championship->organisation->name) }}"
                        class="v2-link">{{ $championship->organisation->name }}</a>
                    &middot; {{ $championship->start_date->format('j F Y') }} &ndash;
                    {{ $championship->end_date->format('j F Y') }}
                </p>
            </div>
        </div>

        <section class="v2-section" aria-labelledby="championship-sheet">
            <div class="v2-section-head">
                <h2 id="championship-sheet">Competitions</h2>
            </div>
            {{-- Oldest first, so the sheet reads through the Championship to today and on --}}
            <x-v2.fixtures :past="$past->reverse()" :live="$live" :upcoming="$upcoming" :types="true"
                empty="No competitions in this Championship yet." />
        </section>
    </div>
@endsection
