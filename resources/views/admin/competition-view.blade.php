@extends('layout')

@section('title')
{{ $comp->name }} | Competitions | Admin
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
    <a href="{{ route('admin.comp.view', $comp) }}">{{ $comp->name }}</a>
</div>

@endsection

@section('content')
<h2>{{ $comp->name }}</h2>
<p>
    <strong>User email:</strong> {{$comp->getUser?->email ?: 'N/A' }}
</p>
<br>

@if ($comp->getUser)
<form action="{{ route('admin.comp.update.userPassword', $comp) }}" method="post" onsubmit="return confirm('Are you sure you want to reset this accounts password?')">
    @csrf
    <button class="btn btn-danger">Reset account password</button>
</form>
@endif


<br></br>


<h3 class="mb-0">Update Competition</h3>
<br>
<form action="{{ route('admin.comp.update.post', $comp) }}" method="post">
    @csrf
    <div class="grid-4">
        <x-form-input id="name" title="Name" required placeholder="Uni Year (e.g. Warwick 2023)" defaultValue="{{$comp->name}}"></x-form-input>
        <x-form-input id="when" title="When" required type="datetime-local" defaultValue="{{$comp->when}}"></x-form-input>
    </div>
    <button type="submit" class="btn">Save</button>
</form>
@endsection