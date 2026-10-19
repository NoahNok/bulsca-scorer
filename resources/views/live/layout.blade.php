<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">


    <x-favicon />
    <title>{{ $comp->name }} | Live | Scoring.Events</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/js/legacy-app.js')
    @vite('resources/css/app.css')
    <script defer src="/vendor/alpinejs-collapse@3.13.3/cdn.min.js"></script>
    <script defer src="/vendor/alpinejs@3.13.3/cdn.min.js"></script>
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">


    @yield('content')


    <x-site-footer />
</body>

</html>
