@extends('digitaljudge.layout')







@php
    $step = request('step', 'email');
@endphp


@section('content')
    <div class="h-screen w-screen flex flex-col items-center justify-center space-y-4"  >



        <h1 class=" font-archivo font-semibold">Scoring.<span class="text-se">Events</span></h1>
        <br>
        <h2 class="font-bold">DigitalJudge</h2>

        <form action="{{ route('judge.login.post') }}" method="POST">
            <div class="form-input">
                <input type="email" id="email" value="{{ request()->input('email', old('email')) }}"  required
                    
                    class="text-center" name="email" placeholder="Email">
                @error('email')
                    <small class="ml-auto">{{ $message }}</small>
                @enderror
            </div>

            

            @if($step == 'pin')

                <div class="form-input">
                  <input type="number" id="pin" value="{{ request()->input('pin', old('pin')) }}" maxlength="6" required
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    class="text-center" name="pin" placeholder="PIN">
                @error('pin')
                    <small class="ml-auto">{{ $message }}</small>
                @enderror
            </div>

            
            @endif
      
            @csrf

            <button class="btn w-full">Login</button>
        </form>

        @if ($step == 'pin')
        <form action="{{ route('judge.login.resend-pin') }}" method="POST">
            <div class="form-input">
                <input type="email"  value="{{ request()->input('email', old('email')) }}" hidden  required
                    
                    class="text-center" name="email" placeholder="Email">
           
            </div>
                   <button type="submit" class="btn w-full">Resend PIN</button>
                   </form>
        @endif


        <small class="text-center" style="
    width: 60%;
">Personal devices are used at <strong>your own risk</strong>.
            Scoring.Events, the event host and affiliated parties assume no responsibility for any <strong>loss, damage, or
                malfunction</strong> of devices during or as a result of participating in this event.</small>
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
