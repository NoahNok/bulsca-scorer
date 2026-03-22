@extends('layouts.guest')

@section('title', __('Forgot Password'))
@section('content')
    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[90%] md:w-[25%]">
            <h1 class="mb-4  text-center">Forgot password</h1>


            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="se-form-input">





                    <input type="email" name="email" id="email" :value="old('email')" required placeholder="Email"
                        autofocus />


                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="se-btn se-btn-outline-primary">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
