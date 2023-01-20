<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schema->name }} | {{ $comp->name }} | Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">

</head>

<body class="overflow-x-hidden">
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class="w-32 h-32" alt="">
            <div class="flex flex-col">
                <h2 class="font-bold mb-0">{{ $schema->name }}</h2>
                <h4>{{ $comp->name }}</h4>
            </div>
        </div>

        <div class="">
            <div class="flex justify-between items-center mx-3 lg:mx-0">
                <h3>Results</h3>
                <a class="link" href="{{ route('public.results.comp', $comp->resultsSlug()) }}"><small>Back</small></a>
            </div>
            <div class="  relative overflow-x-auto w-screen  lg:max-w-[80vw] max-h-[90vh] lg:max-h-[80vh]  ">
                <table class=" text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse relative">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>


                            @foreach ($results[0] as $key => $value)

                            @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                            @endif

                            <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-50 whitespace-nowrap @if($key=='team') left-0 z-20 @endif">
                                {{ str_replace("_", " ", preg_replace("/_[0-9]/mi", "", $key)) }}
                            </th>
                            @endforeach


                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($results as $result)
                        <tr class="bg-white border-b text-right ">
                            @foreach ($result as $key => $value)
                            @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                            @endif
                            <td class="py-3 px-6 text-black text-sm whitespace-nowrap @if($key=='team') sticky left-0 bg-white @endif">
                                @if ($key == "team")
                                <span class="font-semibold">{{ $value }}</span>
                                @else

                                @if (str_contains($key, "rsp"))
                                ({{ $result->{$key . "_places"} }})

                                @endif

                                {{ round((float)$value) }}
                                @endif

                            </td>
                            @endforeach



                        </tr>
                        @empty
                        <tr class="bg-white border-b text-right ">
                            <th colspan="100" scope="row" class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                                None
                            </th>
                        </tr>
                        @endforelse



                    </tbody>

                </table>
            </div>

        </div>

        <div class="pt-8 pb-16">
            <small>
                &copy; BULSCA 2023
            </small>
        </div>





    </div>

</body>

</html>