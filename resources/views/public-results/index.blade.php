<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden">
    <div class="w-screen h-screen flex flex-col items-center overflow-x-hidden lg:justify-center space-y-2 my-8 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-60 h-60" alt="">
        <h1 class="font-bold">Results</h1>

        <hr class="w-96">

        <div class=" w-[80%] lg:w-[65%] xl:w-[50%]">

            @forelse (\App\Models\Season::orderBy('name','desc')->get() as $season)
                <div class="flex flex-col mb-6">
                    <h4 class="-mb-2">{{ $season->name }}</h4>
                    <div class="flex flex-wrap row  w-full justify-center justify-items-center">
                        @forelse ($season->getCompetitions->where('public_results', true)->where('isLeague', true) as $comp)
                            <a href="{{ route('public.results.comp', $comp->resultsSlug()) }}"
                                class="card card-hover flex-grow 2xl:min-w-[23%]  mt-4 mx-2 ">
                                <h4 class="mb-0 text-center">{{ $comp->name }}</h4>
                            </a>
                        @empty
                            <div class="card card-hover grow min-w-[23%] max-w-[30%] mt-4 mx-2 text-center">
                                <p>There are no result for this season available yet!</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            @empty

            @endforelse



        </div>
    </div>

</body>

</html>
