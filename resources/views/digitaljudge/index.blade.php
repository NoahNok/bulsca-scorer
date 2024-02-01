@extends('digitaljudge.layout')

@section('content')
<div class="h-screen w-screen flex flex-col items-center justify-center space-y-4">

    <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-52 h-52 ">
    <br>
    <h2 class="font-bold">DigitalJudge</h2>

    <form action="{{ route('dj.login') }}" method="POST">
        <div class="form-input">
            <input type="number" id="pin" value="{{ request()->input('pin', '') }}" maxlength="6" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="text-center" value="{{ old('pin') }}" name="pin" placeholder="PIN">
            @error('pin')
            <small class="ml-auto">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-input">
            <input type="text" required id="jn" name="judgeName" class="text-center" placeholder="Name or Initials">
            @error('judgeName')
            <small class="ml-auto">{{ $message }}</small>
            @enderror
        </div>
        @csrf

        <button class="btn w-full">Login</button>
    </form>
</div>


    <script>
        window.onload = function() {
            @if (request()->input('pin', '') !== '')
            document.getElementById('jn').focus()
            @else
            document.getElementById('pin').focus()
            @endif
        }
        </script>


@endsection