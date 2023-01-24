@extends('layout')

@section('title')
Admin
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('admin.index') }}">Admin</a>
</div>

@endsection

@section('content')
<h2 class="mb-0">Competitions</h2>
<br>
<div class="grid-4">
    @foreach (\App\Models\Competition::all() as $comp)
    <a href="{{ route('admin.comp.view', $comp) }}" class="flex flex-row items-center shadow-md hover:shadow-lg bg-white p-4 rounded-md border hover:border-black transition-all">
        <h5 class="mb-0">{{ $comp->name }}</h5>
    </a>
    @endforeach
    <x-add-card text="Competition" link="{{ route('admin.comp.create') }}"></x-add-card>

</div>
<br><a href="{{ route('admin.records') }}" class="link">Edit Speed Event Record Times</a>
@endsection