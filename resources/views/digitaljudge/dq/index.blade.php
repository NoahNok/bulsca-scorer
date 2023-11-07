@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
@endphp

@section('content')

    <form method="POST" x-data="{
    
        stage: 1,
    
        nextStage(s) {
            let was = this.stage;
    
            this.stage = s;
            if (s < was) {
                this.resetAhead();
            }
        },
    
        resetAhead() {
            console.log(this.stage)
            if (this.stage < 3) {
                this.$refs.team.value = null;
            }
    
            if (this.type != '' && this.stage < 4) {
                this.type = '';
            }
    
    
        },
    
    
        type: '',
        placeholder: 'Please select a type above',
        setType(ty) {
            this.loading = true;
            this.type = ty;
            this.$refs.code.value = '';
            if (ty == 'dq') { this.placeholder = 'DQXXX'; } else {
                this.placeholder = 'PXXX, PXXX, ...';
            }
            this.nextStage(4);
            this.getCurrent()
        },
        loading: false,
        getCurrent() {
            let event = this.$refs.event.value;
            let team = this.$refs.team.value;
            let type = this.type;
    
            fetch(`./dq/current/${event}/${team}/${type}`).then(res => res.json()).then(data => {
                this.$refs.code.value = data.current;
    
                if (data.current != null) {
                    this.nextStage(5);
                }
    
                this.loading = false;
            })
        },
    }">

        <h4>Select an Event</h4>
        <div class="form-input ">
            <label for="" class="">Event</label>
            <select required name="event" x-ref="event" class="input " style="padding-top: 0.65em; padding-bottom: 0.65em;"
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
                <select required name="team" x-ref="team" class="input "
                    style="padding-top: 0.65em; padding-bottom: 0.65em;" @change="nextStage(3)">
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
                    <button type="button" class="btn w-full" @click="setType('dq')"
                        x-bind:class="type == 'dq' ? 'btn-success' : 'btn-primary'">DQ</button>
                    <button type="button" class="btn w-full" @click="setType('p')"
                        x-bind:class="type == 'p' ? 'btn-success' : 'btn-primary'">Penalty</button>
                </div>
                <input type="hidden" x-model="type" name="type">

                <div x-show="loading">
                    <x-loader />
                </div>
                <div class="form-input " x-show="type != '' && !loading">
                    <input type="text" x-ref="code" name="code" x-bind:placeholder="placeholder"
                        x-mask:dynamic="type == 'dq' ? 'DQ999' : ''" @keyup="nextStage(5)">
                </div>
            </div>
        </div>

        @csrf

        <button x-show="stage > 4" class="btn w-full">Submit</button>
    </form>
@endsection
