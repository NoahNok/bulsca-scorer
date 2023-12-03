@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />';
@endphp

@section('content')

    <form method="POST">

        <h4>Select an Event</h4>
        <div class="form-input ">
            <label for="" class="">Event</label>
            <select required name="event" x-ref="event" class="input " style="padding-top: 0.65em; padding-bottom: 0.65em;"
                @change="nextStage(2)">
                <option value="null">Please select an event...</option>


                @foreach ($comp->getSpeedEvents as $speed)
                    <option value="{{ $speed->id }}">
                        {{ $speed->getName() }}</option>
                @endforeach

            </select>

        </div>

        <div class="form-input ">
            <label for="" class="">Heat & Lane</label>
            <select required name="event" x-ref="event" class="input "
                style="padding-top: 0.65em; padding-bottom: 0.65em;" @change="nextStage(2)">
                <option value="null">Please select a heat and lane</option>

                @foreach ($comp->getHeats->sortBy('heat')->groupBy('heat') as $heat)
                    <optgroup label="Heat {{ $heat[0]->heat }}">
                        @foreach ($heat->sortBy('lane') as $lane)
                            <option value="{{ $lane->id }}">
                                {{ $lane->lane }}: {{ $lane->getTeam->getFullname() }}</option>
                        @endforeach
                    </optgroup>
                @endforeach



            </select>

        </div>

        <div class="grid-2">
            <div class="form-input ">
                <label for="" class="">Turn #</label>
                <input type="text" name="code">

            </div>
            <div class="form-input ">
                <label for="" class="">Length #</label>
                <input type="text" name="code">

            </div>

        </div>

        <div x-show="stage > 2">
            <h4>DQ/Penalty Info</h4>



            <div class="form-input " style="margin-bottom: 0px !important">
                <input type="text" x-ref="code" name="code" placeholder="DQXXX or PXXX"
                    x-mask:dynamic="type == 'dq' ? 'DQ999' : ''" @keyup="nextStage(5)">

            </div>

            <div name="" id="" readonly class="w-full  -mt-3 text-sm text-gray-500 mb-6">
                Manual
                DQ/P description fills here
            </div>

            <label for="">Aditional Details</label>
            <textarea name="" id=""
                class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-none rounded-md">Deets
            </textarea>

        </div>

        <div class="grid-2">
            <div class="form-input " style="margin-bottom: 0px !important">
                <label for="">Seconder</label>
                <input type="text" x-ref="code" name="code" placeholder="Name"
                    x-mask:dynamic="type == 'dq' ? 'DQ999' : ''" @keyup="nextStage(5)">

            </div>
            <div class="form-input " style="margin-bottom: 0px !important">
                <label for="">Position</label>
                <input type="text" x-ref="code" name="code" placeholder="Position"
                    x-mask:dynamic="type == 'dq' ? 'DQ999' : ''" @keyup="nextStage(5)">

            </div>
        </div>









    </form>
@endsection
