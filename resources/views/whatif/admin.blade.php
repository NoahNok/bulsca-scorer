<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin | WhatIf | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body>
    <div class="w-screen h-screen p-3 flex flex-col overflow-x-hidden" x-data="start()">
        <div class="w-full flex  py-2 px-4   items-baseline">
            <h1 class=" text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: 0 !important">
                WhatIf</h1>
            <h3 style="margin-bottom: 0 !important"
                class="text-transparent bg-clip-text bg-gradient-to-r from-bulsca_red to-purple-800">Admin</h3>


        </div>

        <div class="px-4 py-2">
            <h4>Editor Sessions</h4>

            <div class=" grid-4 mb-3">
                @foreach ($comps as $comp)
                    <div class="card">
                        <div class="flex flex-col md:flex-row justify-between md:items-center">
                            <h5 class="hmb-0">{{ $comp->name }}</h5>
                            <small>Last access: {{ $comp->updated_at->format('d/m/y @ H:i') }}</small>
                        </div>
                        <p>
                            {{ \App\Models\User::where('id', $comp->wi_user)->first()?->name ?? 'No User' }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{ $comps->links() }}

        </div>

        <div class="w-full h-full bg-gray-300 bg-opacity-50 flex items-center justify-center z-50 fixed top-0 left-0"
            x-show="loader.show" x-transition>
            <div class="card items-center">
                <x-loader size=12 />
                <p class="text-sm" x-text="loader.message">Please wait while we generate your editor session...</p>
            </div>
        </div>
    </div>




    <div class="alert-banner z-50" id="alert">Test</div>







    <script src="{{ asset('js/alert.js') }}"></script>



    <script>
        console.log('WhatIf Editor v1.0.0')
        console.log('Having a look around are we ;)')

        console.log('⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⣀⣤⣴⣶⡿⢿⡻⢟⡻⣛⡟⡟⣷⢶⣤⣄⡀⠀⢀⠀⠠⢀⠀⢰⣿⣿⣿⣿⣿⣿')
        console.log('⠠⠈⢀⠐⠀⠂⠐⠀⠂⠐⠀⠂⢀⢂⣤⣶⣿⣿⢿⢯⣷⣹⢧⣻⣭⣳⣝⣾⣱⣏⡾⣭⣟⣿⣷⣤⡀⠂⢀⠠⢸⣿⣿⣿⣿⣿⣿')
        console.log('⠀⡐⠀⠠⠈⠀⠂⠁⠐⠈⠀⢰⣴⣿⣿⣿⣯⣿⣯⣿⣾⣽⣿⣷⣿⣷⣿⣾⣷⣯⣿⣳⣟⣾⣽⣟⣿⣦⠀⠀⣾⣿⣿⣿⣿⣿⣿')
        console.log('⠀⡀⠐⠀⡀⠁⠠⠈⠀⢠⣵⣿⣿⣿⣿⣿⣿⣷⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⣿⣿⣯⣿⢿⣿⣿⣷⡀⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⢀⠐⠀⠀⠐⠀⠀⣱⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⡀⠀⠁⠀⠀⣱⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣧⠙⢿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⢀⠀⠁⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠟⠉⠁⣀⡉⠛⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀⠀⠈⠛⠿⣿')
        console.log('⠀⠀⠁⠀⠀⠀⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣏⠀⠠⣾⣿⣿⣷⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡏⠀⠀⠀⠀⠀⢾')
        console.log('⠀⠀⠀⠀⠀⢰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡀⠀⠻⠿⠿⠋⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠁⠀⠀⠀⠀⠀⢸')
        console.log('⠀⠀⠀⠀⠀⠸⣿⡿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⣤⣤⣴⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣟⢯⣛⢷⣲⣾')
        console.log('⠀⠀⠀⠀⠀⢰⡏⢠⣍⢿⣿⣿⣿⣿⣿⣿⣿⣿⢿⡿⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣞⣷⣯⣿')
        console.log('⠀⠀⠀⠀⠀⠈⢳⣸⣿⡞⣿⣿⣿⣿⡟⢯⠹⡘⠦⡉⢖⡡⢏⡿⣻⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣯⣷⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⢳⡛⢃⣿⣿⠿⣭⡙⢆⢣⡙⠴⣉⢦⡹⣎⣷⣻⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠹⣿⣿⢏⡿⣴⡹⣎⢦⣝⣮⣳⢯⣷⣻⢾⣽⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠘⢿⢯⢾⣵⣻⣽⣻⣞⡷⣯⣟⡾⣽⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢘⣯⢟⣼⣳⣳⢯⢾⣝⣳⣾⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠘⠿⢶⣭⡷⠯⠟⠚⢛⣿⣿⣿⣿⣿⣿⠿⠛⠛⠛⠛⠛⠿⠿⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠁⠀⠀⠀⢀⣾⣿⣿⣿⣿⡟⠁⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠉⠙⠻⢿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸⣿⣿⣿⣿⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠛⠿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⣿⣿⣿⣿⡏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸⣿⣿⣿⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⢻')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⣿⣿⣿⣿⣿⣷⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈')
        console.log('   NOOT')
        console.log('      NOOT')
    </script>
</body>

</html>
