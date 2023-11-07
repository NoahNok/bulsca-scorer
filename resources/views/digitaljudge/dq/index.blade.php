@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
@endphp

@section('content')

    <div x-data="{
    
        stage: 1,
    
        nextStage(s) {
            this.stage = s;
        },
    
    
        type: '',
        placeholder: 'Please select a type above',
        setType(ty) {
            this.type = ty;
            if (ty == 'dq') {
                this.placeholder = 'DQXXX';
            } else {
                this.placeholder = 'PXXX, PXXX, ...';
            }
    
        },
    
    }">

        <h4>Select an Event</h4>
        <div class="form-input ">
            <label for="" class="">Event</label>
            <select required name="event" class="input " style="padding-top: 0.65em; padding-bottom: 0.65em;"
                @change="nextStage(2)">
                <option value="null">Please select an event...</option>
                <optgroup label="SERCs">
                    @foreach ($comp->getSERCs as $serc)
                        <option value="se:{{ $serc->id }}">
                            {{ $serc->getName() }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Speeds">
                    @foreach ($comp->getSpeedEvents as $speed)
                        <option value="sp:{{ $speed->id }}">
                            {{ $speed->getName() }}</option>
                    @endforeach
                </optgroup>
            </select>

        </div>



        <div x-show="stage > 1">
            <h4>Select a Team</h4>
            <div class="form-input ">
                <label for="" class="">Event</label>
                <select required name="event" class="input " style="padding-top: 0.65em; padding-bottom: 0.65em;"
                    @change="nextStage(3)">
                    <option value="null">Please select a team...</option>

                    @foreach ($comp->getCompetitionTeams as $team)
                        <option value="{{ $team->id }}">
                            {{ $team->getFullname() }}</option>
                    @endforeach


                </select>

            </div>
        </div>

        <div x-show="stage > 2">
            <h4>DQ/Penalty Info</h4>

            <div>
                <div class="flex w-full space-x-3 mb-3">
                    <button class="btn w-full" @click="setType('dq')"
                        x-bind:class="type == 'dq' ? 'btn-success' : 'btn-primary'">DQ</button>
                    <button class="btn w-full" @click="setType('p')"
                        x-bind:class="type == 'p' ? 'btn-success' : 'btn-primary'">Penalty</button>
                </div>
                <input type="hidden" x-modal="type" name="dq_or_pen">

                <div class="form-input " x-show="type != ''">
                    <input type="text" x-bind:placeholder="placeholder" x-mask:dynamic="type == 'dq' ? 'DQ999' : ''"
                        @keyup="nextStage(4)">
                </div>
            </div>
        </div>



        <button x-show="stage > 3" class="btn w-full">Submit</button>
    </div>
@endsection
