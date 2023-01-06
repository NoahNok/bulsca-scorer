@extends('layout')

@section('title')
Competitions
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>

@endsection

@section('content')
<h2 class="mb-0">Competitions</h2>
<br>
<div class="grid-4">
    @foreach ($comps as $comp)
    <a href="{{ route('comps.view', $comp) }}" class="flex flex-row bg-white p-4 rounded-md border hover:border-black transition-colors">
        <h5 class="mb-0">{{ $comp->name }}</h5>
    </a>
    @endforeach

</div>
@endsection