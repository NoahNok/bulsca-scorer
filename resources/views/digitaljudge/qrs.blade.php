<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRS</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
</head>

<body class="flex flex-col space-y-3 items-center mt-52 w-screen h-screen">
    <h2>{{ $comp->name }}</h2>
    @php
        // signs them in if needed, then adds them to the competition as an official
        $link = route('judge.join-link', $comp->digitalJudgePin);
    @endphp
    {!! QrCode::size(300)->style('round')->generate($link) !!}
    <small class="text-center">
        {{ $link }}
        <br>
        PIN: {{ $comp->digitalJudgePin }}
    </small>
    <h3>Judge Login</h3>

</body>
<script>
    window.onload = function() {
        window.print()
    }
</script>

</html>
