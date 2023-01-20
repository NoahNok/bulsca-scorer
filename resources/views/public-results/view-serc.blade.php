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
            <div class="  relative overflow-x-auto w-screen  lg:max-w-[80vw] max-h-[90vh] lg:max-h-[80vh]  ">
                <table class=" text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse relative">

                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 sticky z-10  ">
                        <tr class="">
                            <th scope="col" class="py-3 px-6 text-left sticky left-0 top-0 bg-gray-50 z-20">
                                Team
                            </th>

                            @foreach ($event->getJudges as $judge)
                            @foreach ($judge->getMarkingPoints as $markingPoint)
                            <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-50" style="writing-mode: vertical-rl; ">
                                {{ $markingPoint->name }}
                            </th>

                            @endforeach

                            @endforeach

                            <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-50">
                                DQ
                            </th>

                            <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-50">
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-50">
                                Position
                            </th>


                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($event->getResults() as $result)
                        <tr class="bg-white border-b text-right  ">
                            <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white  ">
                                {{ $result->team }}
                            </th>

                            @foreach ($event->getJudges as $judge)
                            @foreach ($judge->getMarkingPoints as $markingPoint)
                            <td class="py-3 px-6">
                                {{ $markingPoint->getScoreForTeam($result->tid) }}
                            </td>

                            @endforeach

                            @endforeach

                            <td class="py-4 px-6">
                                N/A
                            </td>

                            <td class="py-4 px-6">
                                {{ round($result->points) }}
                            </td>
                            <td class="py-4 px-6">
                                {{ $result->place }}
                            </td>


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