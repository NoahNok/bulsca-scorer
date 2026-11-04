{{--
    Every host's 404 (main domain, app., judge., live., results. and the rest), on the v2 shell. The first link is
    the way back into wherever the visitor was. Inertia visits load it as a full page (App\Exceptions\Handler).
--}}
@extends('landing.v2.layout')

@section('title', 'Page not found')

@section('content')
    @php
        $back = match (request()->getHost()) {
            \App\Helpers\RouteHelpers::domainRemap('app.') => ['url' => route('manage.home'), 'label' => 'Your competitions'],
            \App\Helpers\RouteHelpers::domainRemap('judge.') => ['url' => route('dj.landing'), 'label' => 'Digital Judge'],
            default => ['url' => \App\Helpers\RouteHelpers::mainRoute('explore'), 'label' => 'Explore competitions'],
        };
    @endphp

    <div class="v2-container pb-16">
        <div class="pt-12">
            <h1 class="v2-page-title">Page not found</h1>
            <p class="v2-page-lead">This link may be out of date, or the page may have moved or been removed.</p>
            <div class="mt-6 flex flex-wrap gap-2">
                <a href="{{ $back['url'] }}" class="v2-btn v2-btn-primary">{{ $back['label'] }}</a>
                <a href="{{ \App\Helpers\RouteHelpers::mainRoute('home') }}" class="v2-btn">Scoring.Events home</a>
            </div>
        </div>
    </div>
@endsection
