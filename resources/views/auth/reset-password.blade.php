@extends('layouts.guest')

@section('title', __('Forgot Password'))
@section('content')
    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[25%]">
            <h1 class="mb-4  text-center">Reset password</h1>
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="se-form-input">
                    <x-input-label for="email" :value="__('Email')" />
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required
                        autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class=" se-form-input">
                    <x-input-label for="password" :value="__('Password')" />
                    <input id="password" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="se-form-input">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                    <input id="password_confirmation" type="password" name="password_confirmation" required />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button class="se-btn se-btn-outline-primary" type="submit">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
