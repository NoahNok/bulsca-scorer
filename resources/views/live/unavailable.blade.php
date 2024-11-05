<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unavailable | Live | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">

    <div class="w-[90vw] md:w-[70vw] my-12 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-60 h-60 " alt="">
        <br>

        <h3>Live is currently unavailable</h3>
        <p>{{ $message }}</p>

        <small>Please check back later.</small>
        <br>

        <br>
        <h4>Previously</h4>


        <div class="grid-3">
            @foreach (\App\Models\Competition::orderBy('when', 'desc')->limit(6)->get() as $comp)
                <a href="{{ route('live', ['comp' => $comp]) }}" class="card card-hover items-center">
                    <img src="@if (isset($brand)) {{ $brand->getLogo() }}@else https://www.bulsca.co.uk/storage/logo/blogo.png @endif"
                        class=" w-20 h-20 mb-3" alt="">
                    <h5> {{ $comp->name }}</h5>
                </a>
            @endforeach
        </div>


    </div>


</body>

</html>
