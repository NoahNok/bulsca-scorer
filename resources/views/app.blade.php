<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo:ital,wght@0,100..900;1,100..900&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">

        
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
               <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body>
        <x-inertia::app />
    </body>
</html>