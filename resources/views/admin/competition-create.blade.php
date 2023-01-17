@extends('layout')

@section('title')
Create Competition | Admin
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('admin.index') }}">Admin</a>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('admin.comp.create') }}">Create Competition</a>
</div>

@endsection

@section('content')
<h2 class="mb-0">Create Competition</h2>
<br>
<form action="{{ route('admin.comp.create.post') }}" method="post">
    @csrf
    <div class="grid-4">
        <x-form-input id="name" title="Name" required placeholder="Uni Year (e.g. Warwick 2023)"></x-form-input>
        <x-form-input id="when" title="When" required type="datetime-local"></x-form-input>
    </div>
    <button type="submit" class="btn">Add</button>
</form>
@endsection