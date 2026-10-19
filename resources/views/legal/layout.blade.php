@extends('layouts.landing')

{{-- Shared shape for the Terms, Privacy and Cookie policies: title, last-updated date, then the sections. --}}

@section('content')
    <article class="legal max-w-3xl">
        <h2 class="mb-1">@yield('title')</h2>
        <p class="text-sm text-gray-500">Last updated
            {{ \Illuminate\Support\Carbon::parse(config('legal.updated'))->format('j F Y') }}</p>

        @yield('policy')

        <h3>Contact</h3>
        <p>{{ config('legal.trading_as') }} is run by {{ config('legal.operator') }}, a sole trader based in the United
            Kingdom. For anything about these policies or your data, email
            <a class="link" href="mailto:{{ config('legal.contact_email') }}">{{ config('legal.contact_email') }}</a>.
        </p>

        <p class="mt-6 text-sm text-gray-500">
            See also the <a class="link" href="{{ route('legal.terms') }}">Terms of Service</a>,
            <a class="link" href="{{ route('legal.privacy') }}">Privacy Policy</a> and
            <a class="link" href="{{ route('legal.cookies') }}">Cookie Policy</a>.
        </p>
    </article>

    <style>
        .legal h3 {
            margin-top: 2rem;
            margin-bottom: 0.5rem;
        }

        .legal p,
        .legal li {
            line-height: 1.6;
        }

        .legal p+p {
            margin-top: 0.75rem;
        }

        .legal ul {
            list-style: disc;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .legal ul li+li {
            margin-top: 0.25rem;
        }
    </style>
@endsection
