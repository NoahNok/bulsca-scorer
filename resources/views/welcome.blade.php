@extends('layouts.guest')




@section('content')
    <div class="flex flex-col items-center justify-center h-[50vh]  relative ">





        <h1 class="font-archivo font-semibold text-7xl!  z-20">Scoring.</h1>
        <h1 class="font-archivo font-semibold text-se text-7xl! z-20 ">Events</h1>

        <div class="shape shape-square blur-sm absolute top-2/8 left-[55%] rotate-12 size-40 animate-pulse animate-6s">
        </div>

        <div
            class="shape shape-square bg-black! blur-lg absolute top-3/4 left-1/3 rotate-[67deg] size-28 animate-pulse animate-8s">
        </div>

        <div class="shape shape-circle absolute blur-3xl size-150 -top-75 left-[100%]"></div>

        <div class="shape shape-circle bg-black! blur-md absolute size-40 top-10 left-24  "></div>


        <div class=" absolute blur-md top-32 left-36 z-11 rotate-104 animate-pulse">
            <div class="shape shape-triangle   "></div>
        </div>




    </div>

    <div class="flex   items-center justify-evenly mt-12">

        <h1 class="max-w-[15ch] text-5xl!">Lifesaving Event Scoring <span class="text-se">revamped</span></h1>


        <img src="{{ asset('launch_event.svg') }}" class=" w-[30%]" alt="">
    @endsection
