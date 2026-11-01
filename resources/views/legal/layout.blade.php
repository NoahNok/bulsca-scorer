@extends('landing.v2.layout')

{{-- Shared shape for the Terms, Privacy and Cookie policies: title, last-updated date, then the sections. --}}

@section('content')
    <div class="v2-container">
        <article class="v2-prose py-12">
            <h1>@yield('title')</h1>
            <p class="v2-prose-meta">Last updated
                {{ \Illuminate\Support\Carbon::parse(config('legal.updated'))->format('j F Y') }}</p>

            @yield('policy')

            <h3>Contact</h3>
            <p>{{ config('legal.trading_as') }} is run by {{ config('legal.operator') }}, a sole trader based in the United
                Kingdom. For anything about these policies or your data, email
                <a class="v2-link-inline" href="mailto:{{ config('legal.contact_email') }}">{{ config('legal.contact_email') }}</a>.
            </p>

            <p class="v2-prose-meta">
                See also the <a class="v2-link-inline" href="{{ route('legal.terms') }}">Terms of Service</a>,
                <a class="v2-link-inline" href="{{ route('legal.privacy') }}">Privacy Policy</a> and
                <a class="v2-link-inline" href="{{ route('legal.cookies') }}">Cookie Policy</a>.
            </p>
        </article>
    </div>
@endsection
