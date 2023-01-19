<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">

</head>

<body>
    <div class="w-screen h-screen flex flex-col items-center justify-center space-y-2">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-60 h-60" alt="">
        <h1 class="font-bold">Results</h1>

        <hr class="w-96">

        <div class="flex flex-wrap row  w-[80%] lg:w-[65%] xl:w-[50%] justify-center justify-items-center">

            @forelse ($comps as $comp)
            <a href="{{ route('public.results.comp', $comp->resultsSlug()) }}" class="card card-hover grow 2xl:min-w-[23%] 2xl:max-w-[23%] mt-4 mx-2">
                <h4 class="mb-0 text-center">{{ $comp->name }}</h4>
            </a>
            @empty
            <div class="card card-hover grow min-w-[23%] max-w-[30%] mt-4 mx-2 text-center">
                <p>There aren't currently any competition results available!</p>
            </div>

            @endforelse

        </div>
    </div>

</body>

</html>