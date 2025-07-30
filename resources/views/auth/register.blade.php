@extends('layouts.guest')

@section('title', __('Log in'))

@section('content')

    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[25%]">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <h1 class="mb-4  text-center">Sign-up</h1>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="se-form-input">
                    <label for="name">Name</label>

                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Mr Scoring"
                        required autofocus />

                    @error('name')
                        <small>{{ $message }}</small>
                    @enderror

                </div>

                <!-- Email Address -->
                <div class="se-form-input -my-2">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                        required />

                    @error('email')
                        <small>{{ $message }}</small>
                    @enderror

                </div>

                <!-- Password -->
                <div class=" se-form-input ">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Password" required />
                    @error('password')
                        <small>{{ $message }}</small>
                    @enderror

                </div>

                <div class=" se-form-input -mt-2">
                    <label for="password_confirmation">Password Confirmation</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        placeholder="Password Confirmation" required />

                    @error('password_confirmation')
                        <small>{{ $message }}</small>
                    @enderror
                </div>


                <!-- Remember Me -->


                <div class="flex items-center justify-between mt-4">


                    <button type="submit" class="se-btn se-btn-outline-primary w-full">
                        {{ __('Sign-up') }}
                    </button>
                </div>
            </form>
        </div>

        <div class="shape shape-square blur-sm absolute top-4/5 left-1/4 rotate-[67deg] size-40 animate-pulse animate-6s">
        </div>

        <div
            class="shape shape-square bg-black! blur-lg   absolute top-2/8 left-[55%] rotate-12 size-28 animate-pulse animate-8s">
        </div>

        <div class="shape shape-circle absolute blur-3xl size-75 top-35 left-[90%] z-10"></div>

        <div class="  absolute blur-md size-40 top-[72%] left-[90%] rotate-93">
            <div class="shape shape-triangle  bg-black!"></div>
        </div>

        <div class="shape shape-circle bg-black! blur-md absolute size-40 top-165 -left-16  "></div>


        <div class=" absolute blur-md top-32 left-36 z-11 rotate-104 animate-pulse">
            <div class="shape shape-triangle   "></div>
        </div>
    </div>




@endsection
