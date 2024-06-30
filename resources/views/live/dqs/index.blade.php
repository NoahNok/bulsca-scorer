@extends('live.layout')

@section('content')
    <div class="w-[90vw] md:w-[70vw] my-12">
        <div class="w-full flex items-center justify-between">
            <h1 class="mb-0">{{ $comp->name }}</h1>
            <h2 id="time-now"></h2>
        </div>
        <br>
        <h3 class="-mb-1">DQs & Penalties</h3>
        <a href="{{ route('live', request()->all()) }}" class="link "><span class="text-sm">Back</span></a>
        <br>
        <br>
        <h4>SERCs</h4>
        <div class="flex flex-col mb-2">
            @foreach ($comp->getSercs as $serc)
                <a href="{{ route('live.dqs.event', array_merge(['se:' . $serc->id], request()->all())) }}" class="link">{{ $serc->getName() }}</a>
            @endforeach
        </div>


        <h4>Speeds</h4>
        <div class="flex flex-col">
            @foreach ($comp->getSpeedEvents as $speed)
                <a href="{{ route('live.dqs.event', array_merge(['sp:' . $speed->id], request()->all())) }}" class="link">{{ $speed->getName() }}</a>
            @endforeach
        </div>




</body>
@endsection
