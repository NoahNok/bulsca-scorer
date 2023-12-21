<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WhatIf | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body>
    <div class="w-screen h-screen flex flex-col items-center justify-center bg-whatif" x-data="{
        loader: {
            show: false,
            message: 'Please wait while we generate your account and editor session...'
        },
    
    
    }">
        <div class="" x-data="{
            openSection: '{{ $errors->any() ? 'start' : 'main' }}' == 'start' ? (window.location.hash == '#resume' ? 'resume' : 'start') : 'main',
        
            setOpenSection(section) {
                this.openSection = section
            }
        }">
            <h3 class="-mb-6">BULSCA</h3>
            <h1 class=" text-[7rem] text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: -0.5rem !important ">
                WhatIf</h1>
            <small class="">WhatIf my team did...?</small>
            <br>
            <br>

            <div class="w-full flex flex-col space-y-3" x-show="openSection == 'main'">
                <button class="btn btn-purple " @click="setOpenSection('start')">Start exploring...</button>
                <button class="btn btn-white btn-thin" @click="setOpenSection('resume')">Resume exploring...</button>
            </div>


            <form method="POST" action="{{ route('whatif.clone') }}" x-on:submit="loader.show=true"
                class="w-full flex flex-col" x-show="openSection == 'start'" style="display: none">
                @csrf

                <x-form-input id="email" title="Email" type="email" required></x-form-input>
                <x-form-input id="password" title="Password" type="password" required></x-form-input>




                <div class="form-input">
                    <label for="competition">Select a competition</label>
                    <select name="competition" id="competition" class="input"
                        style="padding-top: 0.65em; padding-bottom: 0.65em; margin-bottom: 0px !important">
                        @foreach (\App\Models\Season::all() as $season)
                            <option value="null">Please select a competition</option>
                            <optgroup label="{{ $season->name }}">
                                @foreach ($season->getCompetitions()->where('public_results', true)->get() as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>


                </div>

                <button class="btn">Begin</button>
            </form>

            <form method="POST" action="{{ route('whatif.resume') }}?s" class="w-full flex flex-col "
                x-show="openSection == 'resume'" style="display: none">
                @csrf
                <x-form-input id="email" title="Email" type="email" required></x-form-input>
                <x-form-input id="password" title="Password" type="password" required></x-form-input>
                <button class="btn">Resume</button>
            </form>

        </div>

        <div class="w-full h-full bg-gray-300 bg-opacity-50 flex items-center justify-center z-50 fixed top-0 left-0"
            x-show="loader.show" x-transition style="display: none">
            <div class="card items-center">
                <x-loader size=12 />
                <p class="text-sm" x-text="loader.message">Please wait while we generate your editor session...</p>
            </div>
        </div>

    </div>
    <div class="alert-banner z-50" id="alert">Test</div>

    <div class="bg-white z-50 lg:hidden fixed w-screen h-screen top-0 left-0 flex flex-col items-center justify-center">
        <div>
            <h3 class="-mb-6">BULSCA</h3>
            <h1 class=" text-[7rem] text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: 0 !important">
                WhatIf</h1>
            <p class="text-center">WhatIf is not available on mobile devices!</p>
        </div>
    </div>


    <script src="{{ asset('js/alert.js') }}"></script>
</body>

</html>
