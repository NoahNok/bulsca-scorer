{{-- The v2 component catalogue on its own page, for reference while building. Unlinked and not indexed. --}}
@extends('landing.v2.layout')

@section('title', 'Components')

@section('v2-meta')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="v2-container">
        @include('landing.v2.partials.kit')
    </div>
@endsection
