<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unavailable | Live | BULSCA</title>

    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    <style>

    </style>
</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">

    <div class="w-[90vw] md:w-[70vw] my-12 ">
        <x-se-title class="text-6xl! ml-0!" />
        <br>

        <p>{{ $message }}</p>


        <br>

        <br>
        <h4>Previously</h4>


        <div class="grid-3">

            @php
                $comps = \App\Models\Competition::where('when', '<=', now())
                    ->where(function ($query) {
                        $query->where('public_results', true)->orWhere('can_be_live', true);
                    })
                    ->orderBy('when', 'desc')
                    ->paginate(9);
            @endphp

            @foreach ($comps as $comp)
                <x-competition-card url="{{ route('live') }}?comp={{ $comp->id }}" :comp="$comp"
                    class=" "></x-competition-card>
            @endforeach


        </div>
        <br>
        {{ $comps->links() }}


    </div>


</body>

</html>
