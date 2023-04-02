@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col  mt-8 space-y-4">

    <div class="flex w-full items-center justify-center space-x-4">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-32 h-32 ">
        <div class="flex flex-col">
            <h5 class="-mb-1 text-sm">DigitalJudge</h5>
            <h3 class="-mb-1">{{ $serc->name }}</h3>
            <h5 class="text-sm">{{ $comp->name }} </h5>
        </div>
    </div>


    <div class="px-4 pb-4 space-y-2">
        <a href="{{ route('dj.judging.home') }}" class="link">Home</a>
        <p>You are <strong class="text-bulsca"> @forelse ($judges as $judge)
                {{ $judge->name }}
                @if (!$loop->last)
                {{ "," }}
                @endif
                @empty
                @endforelse</strong> Marking: <strong class="text-bulsca">{{ $team->getFullname() }}</strong></p>



        <h3>Mark Sheet</h3>

        <form action="" method="post">
            <div class="flex flex-col space-y-6 ">
                @foreach ($judges as $mJudge)
                <h4>{{ $mJudge->name }}</h4>
                @foreach ($mJudge->getMarkingPoints as $mp)
                @php
                $mpValue = $head ? $mp->getScoreForTeam($team->id) : -1;
                @endphp
                <div class="flex flex-col space-y-2 border-b pb-4">
                    <div class="flex justify-between items-center">
                        <p>{{ $mp->name }}</p>
                        <div>
                            <input type="radio" required class="w-0 h-0 peer" value="0" name="mp-{{ $mp->id }}" @if($mpValue==0) checked @endif id="mp-{{ $mp->id }}-0">
                            <label for="mp-{{ $mp->id }}-0" class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-sm bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                                ZERO
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-2 gap-y-4">
                        @for ($i = 1; $i <= 10; $i++) <div class="flex items-center justify-center">
                            <input type="radio" required class="w-0 h-0 peer" value="{{ $i }}" name="mp-{{ $mp->id }}" @if($mpValue==$i) checked @endif id="mp-{{ $mp->id }}-{{ $i }}">
                            <label for="mp-{{ $mp->id }}-{{ $i }}" class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white ">
                                {{ $i }}
                            </label>
                    </div>
                    @endfor
                </div>
                <div class="text-gray-500 pt-2 flex justify-between">
                    <small>Min: {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->min('result')) ?: '0' }}</small><small>Avg: {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->avg('result'), 1) ?: '0' }}</small><small>Max: {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->max('result')) ?: '0' }}</small>
                </div>
            </div>
            @endforeach

            @endforeach
    </div>
    <br>
    <div class="flex flex-row space-x-4">

        <label for="confirm">I acknowledge that the above results cannot be changed after submitting</label>
        <input type="checkbox" required name="" id="confirm">
    </div>
    <br>
    @csrf
    <button type="submit" class="btn w-full">Submit</button>
    </form>

</div>


</div>







</div>
@endsection