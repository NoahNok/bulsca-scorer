@extends('digitaljudge.layout')

@section('content')
<div class=" w-screen flex flex-col items-center mt-8 space-y-4">

    <div class="flex flex-col space-y-4 items-center">
        <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
        <h5 class="font-semibold ">DigitalJudge | Remove Judge</h5>
        <br>
        <p>Please select a judge to remove:</p>


        @foreach ($judges as $judge)



        <form action="" method="post">
            @csrf
            <input type="hidden" name="removeJudgeId" value="{{ $judge->id }}">
            <button class="btn">{{ $judge->name }}</button>
        </form>
        @endforeach

        <br>
        <a href="{{ route('dj.judging.home', $judge) }}" class="btn btn-danger">Back</a>


    </div>

</div>
@endsection