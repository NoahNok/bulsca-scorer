<!DOCTYPE html>
<html lang="en" class="[scrollbar-gutter:stable]">
    <head>
        <meta charset="utf-8" />
        <x-favicon />
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/fonts/google/fonts.css">

        
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
               <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body>
        <x-inertia::app />
    </body>
</html>