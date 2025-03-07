<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Select Editor | WhatIf | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body>
    <div class="modal" x-data="{
        loader: {
            show: false,
            message: 'Please wait while we generate your editor session...'
        },
    
    
    
    }">
        <div class="modal-content">
            <h3>Welcome Back</h3>


            <h5 class="mb-0">Previous Editors</h5>
            <small>Shows the editor name, and when it was last <strong>used</strong></small>
            <div class="w-full mt-2">

                @forelse (auth()->user()->getWhatIfEditors()->orderBy('updated_at', 'desc')->get() as $editor)
                    <a href="{{ route('whatif.switch', $editor->id) }}"
                        class="flex justify-between items-center group hover:text-bulsca hover:font-semibold transition-all">
                        <p>{{ $editor->name }} <small>({{ $editor->updated_at->format('d/m/Y @ H:i') }})</small></p>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" data-slot="icon"
                            class="w-4 h-4 group-hover:animate-pulse  ">
                            <path fill-rule="evenodd"
                                d="M2 10a.75.75 0 0 1 .75-.75h12.59l-2.1-1.95a.75.75 0 1 1 1.02-1.1l3.5 3.25a.75.75 0 0 1 0 1.1l-3.5 3.25a.75.75 0 1 1-1.02-1.1l2.1-1.95H2.75A.75.75 0 0 1 2 10Z"
                                clip-rule="evenodd" />
                        </svg>

                    </a>
                @empty
                    <small>You don't have any open editor sessions.</small>
                @endforelse


            </div>
            <br>
            <h5>New Editor</h5>
            <form action="{{ route('whatif.internalCas') }}" method="POST" class="w-full flex flex-col"
                x-on:submit="loader.show=true">
                @csrf
                <div class="form-input">


                    <select name="competition" id="competition" class="input"
                        style="padding-top: 0.65em; padding-bottom: 0.65em; margin-bottom: 0px !important">

                        @php
                            Config::set('database.default', 'mysql');
                        @endphp
                        <option value="null">Please select a competition</option>
                        @foreach (\App\Models\Season::orderBy('id', 'desc')->get() as $season)
                            <optgroup label="{{ $season->name }}">
                                @foreach ($season->getCompetitions()->where('public_results', true)->orderBy('when', 'desc')->get() as $competition)
                                    <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                        @php
                            Config::set('database.default', 'whatif');
                        @endphp
                    </select>


                </div>
                <button class="btn ml-auto">Start</button>
            </form>
            <br>

            <a href="{{ route('whatif.logout') }}" class="link">Logout</a>



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




    <script src="{{ asset('js/alert.js') }}"></script>
</body>

</html>
