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
      $params = ['pin' => $comp->digitalJudgePin];
      if ($comp->brand) {
        $params['b'] = $comp->brand;
      }
    @endphp
      {!! QrCode::size(300)->style('round')->generate(route('dj.index', $params)) !!}
      <small class="text-center">
        {{ route('dj.index', $params) }}
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