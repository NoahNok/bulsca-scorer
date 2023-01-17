@extends('layout')

@section('title')
Competition Created | Admin
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
<h2 class="mb-0">Competition Created</h2>
<br>
<p>
    <strong class="text-lg">Please copy down these details:</strong><br>
    <strong>Email:</strong> {{ $email }} <br>
    <strong>Password:</strong> {{ $password }}
</p>
<br>
<a href="{{ route('admin.comp.view' ,$comp)}}" class="btn">Continue</a>
@endsection