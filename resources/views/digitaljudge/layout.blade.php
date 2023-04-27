<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigitalJudge</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
</head>

<body class="overflow-x-hidden">



    @yield('content')

    <div class="flex justify-center items-center p-2">
        <a href="https://forms.gle/tdEhubMkPNY3Dpnd7" class="link">Give Feedback</a>
    </div>
    <div class="alert-banner" id="alert">Test</div>

    <script src="{{ asset('js/alert.js') }}"></script>
    @if (Session::has('success'))
    <script>
        showSuccess('{{ Session::get("success") }}')
    </script>

    @endif
    @if (Session::has('alert-error'))
    <script>
        showAlert('{{ Session::get("alert-error") }}')
    </script>

    @endif
</body>

</html>