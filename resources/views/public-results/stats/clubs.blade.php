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
    <div class=" w-[75%] my-28 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <h1 class="font-bold ">Clubs</h1>

        <br>

        <p>
            @foreach (App\Models\Club::getStatableClubs() as $club)
                <a href="{{ route('public.results.stats.club', $club->name) }}" class="link ">
                    {{ $club->name }}
                </a>
                <br>
            @endforeach
        </p>


    </div>



    </div>

</body>

</html>
