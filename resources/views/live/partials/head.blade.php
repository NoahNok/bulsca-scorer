{{-- Shared <head> for the live pages. They're standalone (not layouts.core) because core's header links would resolve to the live. host. --}}
<meta charset="UTF-8">
<x-favicon />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} | Live | Scoring.Events</title>

<link rel="stylesheet" href="/fonts/google/fonts.css">

@vite('resources/js/basic-app.js')
@vite('resources/css/app.css')
<script defer src="/vendor/alpinejs-collapse@3.13.3/cdn.min.js"></script>
<script defer src="/vendor/alpinejs-persist@3.13.3/cdn.min.js"></script>
<script defer src="/vendor/alpinejs@3.13.3/cdn.min.js"></script>
