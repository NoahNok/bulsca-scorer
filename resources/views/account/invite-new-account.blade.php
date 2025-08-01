@extends('layouts.guest')




@section('content')
    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[90%] md:w-[25%] flex items-center flex-col">

            <p>Invite to join</p>
            <h1 class="text-7xl! mb-2  text-center">{{ $invite->getName() }}</h1>

            <small>You will be joining {{ $invite->getType() }}</small>

            <p class="my-4">You <strong>don't have an account</strong> yet. Please provide a name and password to use with
                your new
                account <strong>{{ $invite->email }}</strong></p>

            <form method="POST" action="{{ route('invite.resolve.new-account', [$invite, $invite->email, 'accept']) }}"
                id="new-acc" class="w-2/3">

                @csrf

                <div class="se-form-input mb-0!">
                    <input type="text" name="name" placeholder="Mr Scoring" value="{{ old('name') }}" required>
                    @error('name')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                <div class="se-form-input mb-0!">

                    <input type="password" id="new-password" name="password" placeholder="Password" required>

                </div>

                <div class="se-form-input mb-0!">

                    <input type="password" id="new-password-conf" name="password_confirmation" required
                        placeholder="Password Confirmation">
                    @error('password')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

            </form>


            <div class="grid-2 mt-4">
                <button form="new-acc" class="se-btn se-btn-outline-primary">Accept</button>
                <form method="POST"
                    action="{{ route('invite.resolve.new-account', [$invite, $invite->email, 'decline']) }}">
                    @csrf
                    <input type="text" name="name" placeholder="Mr Scoring" value="DELCINE_INVITE" class="hidden"
                        required>
                    <input type="password" id="new-password" name="password" placeholder="Password" value="DECLINE_INVITE"
                        class="hidden">
                    <input type="password" id="new-password" name="password_confirmation" placeholder="Password"
                        value="DECLINE_INVITE" class="hidden">
                    <button class="se-btn">Decline</button>
                </form>

            </div>


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
