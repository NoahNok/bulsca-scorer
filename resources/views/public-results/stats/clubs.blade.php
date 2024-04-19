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
    <div class=" w-full md:w-[75%] p-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <h1 class="font-bold">Stats</h1>

        <br>

            {!! $fastestTimes !!}

  
        <h3 class="font-semibold mt-4">Clubs</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 4xl:grid-cols-9 gap-4 ">
          
            @foreach ($clubs as $club)

                <a href="{{ route('public.results.stats.club', $club->name) }}"  class="  link flex items-center  space-x-3 mb-2 w-full  ">
                 
                    <img src="https://bulsca.co.uk/api/uni-logo/{{ $club->name }}" class="h-10 w-10 min-w-[2.5rem]  overflow-hidden" onerror="this.src='https://www.bulsca.co.uk/storage/logo/blogo.png'" alt="" srcset="" style="">
                    <span class="text-lg overflow-hidden overflow-ellipsis whitespace-nowrap hover:whitespace-normal">{{ $club->name }}</span>
                </a>

            @endforeach

        </div>

        <br>

        <dib class="grid grid-cols-6 gap-5" style="gap: 3rem 1.25rem">
            {!! $sercStats !!}
        </dib>
    </div>
    </div>
</body>
</html>
