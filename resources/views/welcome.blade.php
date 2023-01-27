@extends('layout')

@section('title')
Dashboard
@endsection

@section('content')
Hello admin user, I'd probably jump to <a href="{{ route('comps') }}" class="link">Competitions</a>
@endsection