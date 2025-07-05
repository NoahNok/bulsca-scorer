<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <title>{{ $comp->name }} | Live | Scoring.Events</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">


    @yield('content')


</body>

</html>
