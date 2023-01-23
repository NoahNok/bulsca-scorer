<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->getName() }} | {{ $comp->name }} | Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

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
                <table id="table" class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse  relative">

                    <thead class="text-xs text-gray-700 text-right uppercase sticky top-0 z-50 ">
                        <tr class="">
                            <th class=""></th>
                            @foreach ($event->getJudges as $judge)
                            <th colspan="{{ $judge->getMarkingPoints->count() }}" class="py-3 px-6 border-x text-center sticky top-0">{{ $judge->name }}</th>

                            @endforeach
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr class="">
                            <th scope="col" class="py-3 px-6 text-left z-20">
                                Team
                            </th>

                            @foreach ($event->getJudges as $judge)
                            @foreach ($judge->getMarkingPoints as $markingPoint)
                            <th scope="col" class="py-3 px-6 @if ($loop->last) border-r @endif @if($loop->first) border-l @endif group max-h-52 whitespace-nowrap overflow-hidden text-ellipsis hover:whitespace-normal" style="writing-mode: vertical-rl; " title="{{ $markingPoint->name }}">

                                {{ $markingPoint->name }}
                                <!--<p class="text-center">{{ number_format($markingPoint->weight, 1)}}</p>-->

                            </th>

                            @endforeach

                            @endforeach

                            <th scope="col" class="py-3 px-6">
                                DQ
                            </th>

                            <th scope="col" class="py-3 px-6">
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Position
                            </th>


                        </tr>
                        <tr class="">
                            <th class=""></th>
                            @foreach ($event->getJudges as $judge)
                            @foreach ($judge->getMarkingPoints as $mp)


                            <th class="py-3 px-6 text-center sticky top-0  @if ($loop->last) border-r @endif @if($loop->first) border-l @endif">{{ $mp->weight }}</th>
                            @endforeach
                            @endforeach
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>

                    </thead>
                    <tbody id="table-body">

                        @forelse ($event->getResults() as $result)
                        <tr class="bg-white border-b text-right   ">
                            <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap  ">
                                {{ $result->team }}
                            </th>

                            @foreach ($event->getJudges as $judge)
                            @foreach ($judge->getMarkingPoints as $markingPoint)
                            <td class="py-3 px-6 text-center">
                                {{ round($markingPoint->getScoreForTeam($result->tid)) }}
                            </td>

                            @endforeach

                            @endforeach

                            <td class="py-4 px-6">
                                {{ $event->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ?: '-' }}
                            </td>

                            <td class="py-4 px-6 ">
                                {{ round($result->points) }}
                            </td>
                            <td class="py-4 px-6 ">
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

    <script>
        function initTable() {
            let table = document.getElementById("table");
            let tableBody = document.getElementById("table-body");
            let tableRows = tableBody.querySelectorAll("tr")

            table.onmouseover = (e) => {
                let type = e.target;
                if (type.nodeName != "TD") return
                //console.log(type.innerHTML)
                let index = Array.from(type.parentNode.children).indexOf(type)


                tableBody.querySelectorAll("tr").forEach(tr => {
                    tr.children[index].classList.add('bg-gray-200')
                })
            }


            table.onmouseout = (e) => {
                let type = e.target;
                if (type.nodeName != "TD") return
                //console.log(type.innerHTML)
                let index = Array.from(type.parentNode.children).indexOf(type)


                tableBody.querySelectorAll("tr").forEach(tr => {
                    tr.children[index].classList.remove('bg-gray-200')
                })
            }



        }

        window.onload = initTable()
    </script>
</body>

</html>