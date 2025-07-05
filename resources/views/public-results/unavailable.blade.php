<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">


    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <title>Unavailable | Results | Scoring.Events</title>



</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">

    <div class="w-[90vw] md:w-[70vw] my-12 ">
        <img src="{{ asset('blogo.png') }}" class=" w-60 h-60 " alt="">
        <br>

        <h3>Results unavailable</h3>
        <p>{{ $message }}</p>

        <small>Please check back later, <a
                href="{{ route('public.results.comp', ['comp_slug' => $comp->resultsSlug()]) }}"
                class="link"><small>or
                    click
                    here</small></a>.</small>
        <br>


    </div>


</body>

</html>
