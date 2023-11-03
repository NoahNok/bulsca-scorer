@extends('digitaljudge.mpa-layout')

@section('title', 'Manage')
@php
    $backlink = false;
@endphp

@section('content')
    <h4>SERCs</h4>
    <div class="flex flex-col space-y-2">
        @foreach (\App\DigitalJudge\DigitalJudge::getClientCompetition()->getSERCs->where('digitalJudgeEnabled') as $serc)
            <a href="{{ route('dj.manage.serc', $serc) }}" class="card card-hover">
                <h5 class="hmb-0">{{ $serc->getName() }}</h5>
            </a>
        @endforeach
    </div>
    <br>


    <h4>Speeds</h4>
    <div class="flex flex-col space-y-2">
        @foreach (\App\DigitalJudge\DigitalJudge::getClientCompetition()->getSpeedEvents->where('digitalJudgeEnabled') as $speed)
            <a href="" class="card card-hover">
                <h5 class="hmb-0">{{ $speed->getName() }}</h5>
            </a>
        @endforeach
    </div>
@endsection
