@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center mb-6">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge</h5>
        <br>
        <h2 class="font-bold">Change Judge?</h2>

        <p class="px-4">The judging URL and you're previously selected judge don't match. If this was an accident click "Back to Judging", if not click "Change Judge" to select a different Judge!</p>



        <a href="{{ route('dj.judging.home', DigitalJudge::getClientJudge() ) }}" class="btn w-[80%]">Back to Judging</a>

        <a href="{{ route('dj.home' ) }}" class="btn btn-danger w-[80%]">Change Judge</a>

    </div>
</div>
@endsection