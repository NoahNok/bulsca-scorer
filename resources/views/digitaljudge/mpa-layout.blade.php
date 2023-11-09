<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalJudge</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="overflow-x-hidden">

    @if (\App\DigitalJudge\DigitalJudge::isClientHeadJudge())
        <div class="w-full fixed top-0 left-0 bg-bulsca text-white text-sm font-semibold text-center z-10 ">
            Head Judge/SERC Setter Mode
        </div>
    @endif


    <div class="{{ $nopad ?? false ? '' : 'p-6' }} md:max-w-[30%] md:mx-auto">
        @if ($backlink ?? false)
            <div class="{{ $nopad ?? false ? 'mx-4 mt-6' : '' }}"> <a href="{{ $backlink ?? route('dj.home') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 hover:-ml-2 transition-all ">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                    </svg>
                </a></div>
        @endif


        <div class="flex justify-between items-center {{ $nopad ?? false ? 'px-4' : '' }}">
            <div>
                <h2 class="-mb-1">@yield('title')</h2>
                <small class="">{{ \App\DigitalJudge\DigitalJudge::getClientCompetition()->name }}</small>
            </div>
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="white" class="w-12 h-12 p-3 bg-bulsca rounded-full">
                    {!! $icon ??
                        '<path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />" ?>' !!}
                </svg>



            </div>
        </div>

        <br>


        @yield('content')

        <br>
        <br>
        <br>
    </div>




    @if (\App\DigitalJudge\DigitalJudge::isClientHeadJudge())
        <div class="fixed left-0 bottom-0 w-screen bg-white border-t-2  grid grid-cols-3 ">
            <a href="{{ route('dj.home') }}" class="p-2 text-center border-r">Judge</a>
            <a href="{{ route('dj.manage.index') }}" class="p-2 text-center border-r">Manage</a>
            <a href="{{ route('dj.dq.index') }}" class="p-2 text-center border-l">DQ/Penalty</a>

        </div>
    @else
        <div class="fixed left-0 bottom-0 w-screen bg-white border-t-2  grid grid-cols-1">
            <a href="{{ route('dj.home') }}" class="p-2 text-center border-r">Judge Home</a>
            {{-- <a href="#" class="p-2 text-center border-r">Help</a> --}}
        </div>
    @endif


    @env('local')
    <div class="fixed right-0 bottom-[15%] text-white bg-red-700 px-1 py-3 font-semibold rounded-l-md  "
        style="writing-mode: vertical-rl">
        <a href="{{ route('LOCAL.dj.toggle-head-ref') }}">DEVELOPMENT</a>
    </div>
    {{-- <div class="fixed  left-0 top-[15%] text-white bg-red-700 px-1 py-3 font-semibold rounded-r-md  "
        style="writing-mode: vertical-rl">
        <a href="{{ route('LOCAL.dj.toggle-head-ref') }}">DEVELOPMENT</a>
    </div> --}}
    @endenv
    <div class="alert-banner" id="alert">Test</div>
    <script src="{{ asset('js/alert.js') }}"></script>
    @if (Session::has('success'))
        <script>
            showSuccess('{{ Session::get('success') }}')
        </script>
    @endif
    @if (Session::has('alert-error'))
        <script>
            showAlert('{{ Session::get('alert-error') }}')
        </script>
    @endif
</body>

</html>
