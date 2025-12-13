@extends('layouts.guest')

@section('title', __('Log in'))

@section('content')

    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[90%] md:w-[25%]">
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <h1 class="mb-4  text-center">Login</h1>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="se-form-input">

                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email"
                        required autofocus />
                    @error('email')
                        <small>{{ $message }}</small>
                    @enderror

                </div>

                <!-- Password -->
                <div class=" se-form-input -mt-2">
                    <input id="password" type="password" name="password" placeholder="Password" required
                        autocomplete="current-password" />
                    @error('password')
                        <small>{{ $message }}</small>
                    @enderror

                </div>


                <div class="flex space-x-2 ">
                    <input type="checkbox" name="remember" id="remember_me">
                    <label for="remember_me">Remember me</label>
                </div>

                <!-- Remember Me -->


                <div class="flex items-center justify-between mt-4">
                    @if (Route::has('password.request'))
                        <a class="link text-xs!" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="se-btn se-btn-outline-primary">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            @if(config('sso.enabled'))
                <div style="text-align: center; margin: 20px 0;">
                    <p>- OR -</p>
                </div>
                
                <a href="{{ route('auth.sso') }}" style="text-decoration: none;">
                    <button type="button" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Sign in with BULSCA SSO
                    </button>
                </a>
            @endif

        </div>

        <div class="shape shape-square blur-sm absolute top-4/5 left-1/4 rotate-[67deg] size-40 animate-pulse animate-6s">
        </div>

        <div
            class="shape shape-square bg-black! blur-lg hidden md:flex  absolute top-2/8 left-[55%] rotate-12 size-28 animate-pulse animate-8s">
        </div>

        <div class="shape shape-circle absolute blur-3xl size-75 top-35 left-[90%] z-10"></div>

        <div class="  absolute blur-md size-40 top-[72%] left-[90%] rotate-93">
            <div class="shape shape-triangle  bg-black!"></div>
        </div>

        <div class="shape shape-circle bg-black! blur-md absolute size-40 top-165 -left-16  "></div>


        <div class=" absolute blur-md -top-10 left-36 z-11 rotate-104 animate-pulse">
            <div class="shape shape-triangle   "></div>
        </div>
    </div>




@endsection
