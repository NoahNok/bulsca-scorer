<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->getName() }} | {{ $comp->name }} | Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">

</head>

<body class="overflow-x-hidden">
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class="w-32 h-32" alt="">
            <div class="flex flex-col">
                <h2 class="font-bold mb-0">{{ $event->getName() }}</h2>
                <h4>{{ $comp->name }}</h4>
            </div>
        </div>

        <div class="">
            <div class="flex justify-between items-center mx-3 lg:mx-0">
                <h3>Results</h3>
                <a class="link" href="{{ route('public.results.comp', $comp->resultsSlug()) }}"><small>Back</small></a>
            </div>
            <div class="  relative overflow-x-auto w-screen lg:w-auto  ">
                <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50  ">
                        <tr class="">
                            <th scope="col" class="py-3 px-6 text-left sticky left-0 bg-gray-50">
                                Team

                            </th>
                            <th scope="col" class="py-3 px-6">
                                @if ($event->getName() == "Rope Throw")
                                Ropes/Time
                                @else
                                Time
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-6">
                                DQ
                            </th>

                            @if ($event->hasPenalties())
                            <th scope="col" class="py-3 px-6">
                                Penalties
                            </th>
                            @endif
                            <th scope="col" class="py-3 px-6">
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Position
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($event->getResults() as $result)
                        <tr class="bg-white border-b text-right ">
                            <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white ">
                                {{ $result->team }}
                            </th>
                            <td class="py-4 px-6">

                                @if ($result->result < 4) {{ $result->result }} @else @php $mins=floor($result->result / 60000);
                                    $secs = (($result->result)-($mins*60000))/1000;
                                    @endphp
                                    {{ sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT)}}
                                    @endif


                            </td>
                            <td class="py-4 px-6">
                                {{ $result->disqualification ?: '-' }}
                            </td>

                            @if ($event->hasPenalties())
                            <td class="py-3 px-6">


                                {{ App\Models\Penalty::where('speed_result', $result->id)->get('code')->implode('code', ', ') ?: ($result->penalties == 0 ? '-' : '') }}
                                @if ($event->getName() == 'Swim & Tow' && $result->penalties != 0)
                                (P900 x{{$result->penalties - App\Models\Penalty::where('speed_result', $result->id)->count() }})
                                @endif
                            </td>
                            @endif
                            <td class="py-4 px-6">
                                {{ round($result->points) }}
                            </td>
                            <td class="py-4 px-6">
                                {{ $result->place }}
                            </td>

                        </tr>
                        @empty
                        <tr class="bg-white border-b text-right ">
                            <th colspan="100" scope="row" class="py-4 px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
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