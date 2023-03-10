@extends('layout')

@section('title')
{{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>

@endsection

@section('content')
<h2 class="mb-0">{{ $comp->name }}</h2>
<br>
<p>Welcome to the scorer for {{ $comp->name }}. If you run into any issues please contact the Data Manager (<a class="link" href="mailto:data@bulsca.co.uk">data@bulsca.co.uk</a>) or find them on the day!

</p>
<br>
<div class="grid-4">
    <a href="{{ route('comps.view.teams', $comp) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">Teams</p>
    </a>
    <a href="{{ route('comps.view.events', $comp) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">Events</p>
    </a>
    <a href="{{ route('comps.view.results', $comp) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">Results</p>
    </a>

</div>
<br>
<h3>Important Notes</h3>
<div class="grid-4">
    <div>
        <h4>Results</h4>
        <ul class=" list-disc list-inside space-y-1">
            <li>The setup of a results sheet cannot be altered after creation. If you need to change a weighting, you'll need to create a new sheet and delete the old one.</li>
            <li>Results sheets automatically update when you change scores so you don't need to worry about having to recreate the sheet every time you make a change to a result.</li>
        </ul>

    </div>
    <div>
        <h4>Time Format</h4>
        <ul class=" list-disc list-inside space-y-1">
            <li>When entering a time please enter it in the following format: <code>xx:xx.xxx</code>, where each x is a digit between 0-9!</li>
            <li>You may omit leading zeros for the minute and seconds but you must include all 3 digits for millis.</li>
            <li>If your stopwatch only reports 2 digits for millis then append a <code>0</code> to the end of the millis time when entering into the results box.</li>
        </ul>
    </div>
    <div>
        <h4>Multiple Penalties</h4>
        <ul class=" list-disc list-inside space-y-1">
            <li>If an event allows for multiple penalties, then they must be entered as a comma (,) separated list.</li>
            <li>Penalties should take the form <code>Pxxx</code> with x being a digit. <strong>They must</strong> be 3 characters long!</li>
        </ul>
    </div>
    <div>
        <h4>Disqualifications</h4>
        <ul class=" list-disc list-inside space-y-1">
            <li><strong>Only one</strong> disqualification should be entered into the disqualification box.</li>
            <li>They should be in the form of <code>DQxxx</code> with x being a digit and there must be 3 digits!</li>
            <li>DQ501 relating to excessive penalties may be left out as the system automatically applies it to relevant events (Swim & Tow)</li>
        </ul>
    </div>
</div>
@endsection