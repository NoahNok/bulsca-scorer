<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $comp->name }} | Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">

</head>

<body>
    <div class="w-screen  min-h-screen flex flex-col items-center lg:justify-center space-y-2 my-4 lg:my-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-60 h-60" alt="">
        <h1 class="font-bold">{{ $comp->name }}</h1>

        <hr class="w-96">
        <br>

        <div class="flex flex-col space-y-4 w-[80%] lg:w-[65%] xl:w-[50%]">
            <div class="">
                <h3>SERCs</h3>
                <div class="grid-4">

                    @forelse ($comp->getSERCs as $serc)
                    <a href="{{ route('public.results.serc', [$comp->resultsSlug(), $serc]) }}" class="card card-hover">
                        <h4 class="mb-0 text-center">{{ $serc->getName() }}</h4>
                    </a>
                    @empty


                    @endforelse

                </div>
            </div>

            <div class="">
                <h3>Speeds</h3>
                <div class="grid-4">

                    @forelse ($comp->getSpeedEvents as $speed)
                    <a href="{{ route('public.results.speed', [$comp->resultsSlug(), $speed]) }}" class="card card-hover">
                        <h4 class="mb-0 text-center">{{ $speed->getName() }}</h4>
                    </a>
                    @empty


                    @endforelse

                </div>
            </div>
            <br>
            <hr>


            <div class="pt-3">
                <h3>Results</h3>
                <div class="grid-4">

                    @forelse ($comp->getResultSchemas as $schema)
                    <a href="{{ route('public.results.results', [$comp->resultsSlug(), $schema]) }}" class="card card-hover">
                        <h4 class="mb-0 text-center">{{ $schema->name }}</h4>
                    </a>
                    @empty


                    @endforelse

                </div>
            </div>

        </div>



    </div>

</body>

</html>