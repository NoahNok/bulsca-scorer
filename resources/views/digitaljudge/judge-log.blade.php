@extends('layout')

@section('title')
    {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="">Judge Log</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Judge Log</h2>
    <br>


    <form action="" method="get">

        <div class="flex justify-between items-center">
            <h5>Filters</h5>
            <button class="btn">Apply Filters</button>
        </div>

        <div class="form-input w-max">
            <label for="judge-filter">Judge</label>
            <select name="filterJudge" id="judge-filter">
                <option value="">All</option>
                @foreach ($comp->getSERCs->where('digitalJudgeEnabled', 1) as $serc)
                    <optgroup label="{{ $serc->getName() }}">
                        @foreach ($serc->getJudges as $judge)
                            <option value="{{ $judge->id }}" @if (Request::input('filterJudge') == $judge->id) selected @endif>
                                {{ $judge->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

    </form>

    {{ $log->links() }}
    <div class="flex flex-col space-y-2 mt-2">
        @forelse ($log as $l)
            <div class="card card-row space-x-4 items-center">
                <p class="text-center text-sm text-gray-500 px-2">
                    {{ $l->created_at->format('h:ia') }}<br>{{ $l->created_at->format('d/m/y') }}</p>
                <div class="px-4">
                    <h5 class=" ">
                        {{ $l->judgeName }} marked {{ $l->getTeam->getFullname() }}
                    </h5>
                    @if ($l->judge == null)
                        <p class=" break-all overflow-hidden ">
                            Event: {{ $l->getSpeedEvent->getName() }}
                            <br>
                            Type: Speed
                            <br>

                        </p>
                    @else
                        <p class=" break-all overflow-hidden ">
                            Event: {{ $l->getJudge()->first()->getSERC->getName() }}
                            <br>
                            Type: SERC
                            <br>
                            Judge Name: {{ $l->getJudge()->first()->name }}
                        </p>
                    @endif
                </div>
            </div>

        @empty
            <p class="text-gray-700 indent-4">No judging log's found, if you have set any filters try clearing them!</p>
        @endforelse

        {{ $log->links() }}
    </div>
@endsection
