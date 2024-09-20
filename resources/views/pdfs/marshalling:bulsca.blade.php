<!DOCTYPE html>
<html lang="en" class="bg-gray-100 w-screen h-screen flex items-center justify-center">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <title>{{ ucfirst($type) }} Marshalling Pack</title>

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

        }
    </style>

    @if ($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endif
</head>



<body class="w-screen h-screen  flex flex-col space-y-12 print:space-y-0 items-center overflow-x-hidden ">


    <div class="min-h-[297mm] min-w-[210mm] bg-white p-5 flex flex-col   items-center justify-center text-center">
        <h1>Marshalling Pack</h1>
        <h3>{{ $comp->name }}</h3>
        <p>{{ $location }}<br>{{ $comp->when->format('jS F Y') }}</p>
        <br>
        <br>
        <ol class="list list-disc capitalize">
            {{ $type }}s
        </ol>
    </div>

    @php
        $data = collect($data);

        $chunks = $type == 'SERC' ? $data->chunk(1) : $data->chunk(3);
    @endphp

    @foreach ($chunks as $chunk)
        <div class="min-h-[297mm]   w-[210mm] bg-white p-5 flex flex-col grow ">
            <div class="flex w-full justify-between items-center">
                <h2 class="hmb-0">Marshalling</h2>
                <p class=" font-semibold text-right break-words">{{ $comp->name }} -
                    {{ $comp->when->format('jS F') }}<br><small>{{ $location }}</small></p>
            </div>

            <br>


            @foreach ($chunk as $group)
                <h4 class="mt-1">{{ $group['name'] }}</h4>
                <ol class=" space-y-1">
                    @foreach ($group['data'] as $name)
                        <li>{{ $name }}</li>
                    @endforeach
                </ol>
            @endforeach




        </div>
    @endforeach


    <script>
        window.onload = function() {
            window.print()
        }
    </script>

</body>

</html>
