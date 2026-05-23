@extends('digitaljudge.layout')









@section('content')
    <div class="h-screen w-screen flex flex-col items-center justify-center space-y-4" x-data="{
    
    
        getName() {
                return localStorage.getItem('judgeName') || '';
            },
    
            setName(name) {
                localStorage.setItem('judgeName', name);
            }
    
    
    
    
    }">



        <h1 class=" font-archivo font-semibold">Scoring.<span class="text-se">Events</span></h1>
        <br>
        <h2 class="font-bold">DigitalJudge</h2>

        <form action="{{ route('dj.login') }}" method="POST">
            <div class="form-input">
                <input type="number" id="pin" value="{{ request()->input('pin', old('pin')) }}" maxlength="6" required
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    class="text-center" name="pin" placeholder="PIN">
                @error('pin')
                    <small class="ml-auto">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-input">
                <input type="text" required id="jn" name="judgeName" class="text-center" :value="getName()"
                    @input="setName($event.target.value)" placeholder="Name or Initials">
                @error('judgeName')
                    <small class="ml-auto">{{ $message }}</small>
                @enderror
            </div>
            @csrf

            <button class="btn w-full">Login</button>
        </form>
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
