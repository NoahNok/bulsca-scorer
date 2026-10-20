@extends('live.layout')

@section('title', 'DQs & Penalties | ' . $comp->name)

@section('content')
    <h2>DQs & Penalties</h2>
    <p class="text-sm text-gray-600 mb-4">Pick an event to see its resolved DQs and penalties.</p>

    @include('live.partials.dq-events')
@endsection
