@extends('layouts.guest')




@section('content')
    <div class="h-[80vh] flex items-center justify-center relative">


        <div class="w-[90%] md:w-[25%] flex items-center flex-col">

            <p>Invite to join</p>
            <h1 class="text-7xl! mb-2  text-center">{{ $invite->getName() }}</h1>

            <small>You will be joining {{ $invite->getType() }}</small>


            <div class="grid-2 mt-4">
                <a href="{{ route('invite.resolve', [$invite, $invite->email, 'accept']) }}"
                    class="se-btn se-btn-outline-primary">Accept</a>
                <a href="{{ route('invite.resolve', [$invite, $invite->email, 'decline']) }}" class="se-btn">Decline</a>
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
