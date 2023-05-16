@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center mb-6">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge</h5>
        <br>
        <h2 class="font-bold">{{ $comp->name }}</h2>

        <h4 class="">Speeds</h4>





        @foreach ($comp->getSpeedEvents as $speed)
        <a href="#" class="btn btn-primary">{{ $speed->getName() }}</a>

        @endforeach
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
    </div>
</div>
@endsection