<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs | Stats | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-full md:w-[75%] m-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <h1 class="font-bold ">Clubs</h1>

        <br>

        {!! $fastestTimes !!}

        <br>

        <div class="grid-3 ">
            <div class="card">
                <p class="columns-3 ">
                    @foreach ($clubs as $club)
                        <a href="{{ route('public.results.stats.club', $club->name) }}" class="link ">
                            {{ $club->name }}
                        </a>
                        <br>
                    @endforeach
                </p>
            </div>

            

     
        </div>


    </div>



    </div>

</body>

</html>
