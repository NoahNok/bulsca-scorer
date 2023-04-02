@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center mb-6">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge | {{ $comp->name }} | {{ $serc->getName() }}</h5>
        <br>
        <h2 class="font-bold">{{ $judge->name }}</h2>

        <p class="px-4">Please check the judging criteria below matches your breif and casualty and then click "Continue"</p>

        <h4>Criteria</h4>
        <ul class=" list-disc ">
            @foreach ($judge->getMarkingPoints as $mp)

            <li>{{ $mp->name }}</li>

            @endforeach
        </ul>
        <br>
        <form action="{{ route('dj.judging.confirm', $judge) }}" method="post" class=" w-[80%]">
            @csrf
            <button type="submit" class="btn w-full">Continue</button>
        </form>
        <a href="{{ route('dj.home') }}" class="btn btn-danger w-[80%]">Back</a>

    </div>
</div>
@endsection