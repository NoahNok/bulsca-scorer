@extends('layouts.guest')




@section('content')
    <div class="flex justify-center  min-h-[50vh]  mt-16  relative  ">

        <div class="absolute -top-35  w-screen  h-110 blur-xs"
            style="background: url({{ asset('pool.jpg') }}); background-size:cover;">
        </div>




        <div class="z-20
            w-full 2xl:w-[75%]  ">
            <h1 class="text-4xl! md:text-6xl! tracking-tight font-extrabold! text-white  ">Score the
                Action.<br><span class="text-se-accent text-5xl! md:text-7xl! ">Own the
                    Moment.</span></h1>

            <br>


            <x-search-all />



            <br>
            <div class="flex gap-4 overflow-x-hidden relative overflow-y-hidden">
                @foreach ($upcoming->chunk(2) as $chunk)
                    <div class="flex flex-col gap-4 ">
                        @foreach ($chunk as $comp)
                            <x-competition-card :comp="$comp" :url="route('landing.competition', $comp->getSlug())" class="w-[300px]" />
                        @endforeach
                    </div>
                @endforeach

                <div
                    class="absolute top-19 right-0 h-full w-20 bg-gradient-to-r from-transparent to-white pointer-events-none">

                </div>

            </div>
            <div class="my-6 flex">
                <a href="{{ route('explore') }}"
                    class="se-btn se-btn-outline-primary ml-auto flex items-center space-x-2 animate-sts  "><span>Explore
                        More</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>


        </div>






        {{-- 
        <div class="shape shape-square blur-sm absolute top-2/8 left-[55%] rotate-12 size-40 animate-pulse animate-6s">
        </div>

        <div
            class="shape shape-square bg-black! blur-lg absolute top-3/4 left-1/3 rotate-[67deg] size-28 animate-pulse animate-8s">
        </div>

        <div class="shape shape-circle absolute blur-3xl size-75 top-55 left-[90%] z-10"></div>

        <div class="  absolute blur-md size-40 top-25 left-[105%] rotate-93">
            <div class="shape shape-triangle  bg-black!"></div>
        </div>

        <div class="shape shape-circle bg-black! blur-md absolute size-40 top-10 left-24  "></div>


        <div class=" absolute blur-md top-32 left-36 z-11 rotate-104 animate-pulse">
            <div class="shape shape-triangle   "></div>
        </div> --}}





    </div>

    <div class="flex  justify-center   relative ">
        <div class="w-[75%]">
            <br>
            <hr class="spacer">
            <br>
            <div class="grid-3">
                <div class="flex flex-col ">
                    <h2>Organisers</h2>
                    <ul class="list-none list-arrow">
                        <li class="">
                            Quick setup
                        </li>
                        <li>
                            Additional accounts with finegrain access
                        </li>
                        <li>
                            Organisations to bundle related competitions
                        </li>
                        <li class="text-se font-semibold">Less paper waste 🌲</li>
                    </ul>
                </div>
                <div class="flex flex-col xl:items-center">
                    <h2>Competitiors</h2>
                    <ul class="list-none list-arrow">
                        <li class="">
                            Instant online results
                        </li>
                        <li>
                            Look at what could have been with <span class="text-se font-semibold">WhatIf</span>
                        </li>
                        <li>
                            Track the day live
                        </li>
                    </ul>
                </div>
                <div class="flex flex-col xl:items-end">
                    <h2>Officials</h2>
                    <ul class="list-none list-arrow">
                        <li class="">
                            <span class="text-se font-semibold">Sleek</span> online officiating
                        </li>
                        <li>
                            Digital disqualifications and penalties
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
