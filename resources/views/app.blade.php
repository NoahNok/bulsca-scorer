<!DOCTYPE html>
<html lang="en" class="[scrollbar-gutter:stable]">
    <head>
        <meta charset="utf-8" />
        <x-favicon />
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <link rel="manifest" href="/judge.webmanifest">
        <link rel="apple-touch-icon" href="/judge-icons/apple-touch-icon.png">
        <meta name="theme-color" content="#ffffff">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="Digital Judge">
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