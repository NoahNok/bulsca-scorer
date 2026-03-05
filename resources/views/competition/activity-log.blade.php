@extends('layouts.competition')

@section('title')
    Activity Log
@endsection



@section('content')
    <h2>Activity Log</h2>

    <br>
    <div>
        <x-activity-log :related="[$comp]" />
    </div>
@endsection
