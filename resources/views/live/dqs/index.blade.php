<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $comp->name }} | Live | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">
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

</html>
