<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if ($comp->areResultsProvisional()) (PROVISIONAL) @endif{{ $schema->name }} | {{ $comp->name }} | Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

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
        <a href="https://forms.gle/FEc8XJM3SyUma3Br6" target="_blank" rel="noopener noreferrer" class="link">Give Feedback</a>
        <div class="">
            @if ($comp->areResultsProvisional())
            <div class="p-2 text-center text-lg">
                <p>These results are provisional! <strong>They are subject to change</strong> and should not be considered accurate or final!</p>
            </div>
            @endif
            <div class="flex justify-between items-center mx-3 lg:mx-0">
                <h3>Results</h3>
                <a class="link" href="{{ route('public.results.comp', $comp->resultsSlug()) }}"><small>Back</small></a>
            </div>
            <div class="  relative overflow-x-auto w-screen  lg:max-w-[80vw] max-h-[90vh] lg:max-h-[80vh]  ">
                <table id="table" class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse relative">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>


                            @foreach ($results[0] as $key => $value)

                            @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                            @endif

                            <th scope="col" class="py-3 px-6  whitespace-nowrap ">
                                {{ str_replace("_", " ", preg_replace("/_[0-9]/mi", "", $key)) }}
                            </th>
                            @endforeach


                        </tr>
                    </thead>
                    <tbody id="table-body">

                        @forelse ($results as $result)
                        <tr class="bg-white border-b text-right    ">
                            @foreach ($result as $key => $value)
                            @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                            @endif
                            <td class="py-3 px-6 text-black text-sm whitespace-nowrap @if($key=='team')  bg-white @endif">
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

            <div class="mt-2 flex items-center justify-between">
                <div class="flex flex-col  ">
                    <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Filter" id="team-filter" style="margin-bottom: 0 !important" type="text"></div>
                    <small class="text-gray-600">team:x, league:x</small>
                </div>

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

            let filter = document.getElementById("team-filter")

            function search(team) {

                tableRows.forEach(row => {
                    let teamCol = row.children[0].children[0];
                    let teamName = teamCol.innerHTML.trim().toLowerCase();

                    team = team.toLowerCase();
                    let hide = false;



                    if (team.startsWith("team:")) {
                        hide = !teamName.endsWith(team.substr(5));
                    } else if (team.startsWith("league:")) {
                        let targetLeague = team.substr(7, 1);
                        if (targetLeague == "a") {
                            hide = !teamName.endsWith(targetLeague);
                        } else {
                            hide = teamName.endsWith("a")
                        }
                    } else {
                        hide = !teamName.includes(team);
                    }

                    row.hidden = hide;


                })

            }
            filter.onkeyup = (e) => {
                search(e.target.value)
            }


        }

        window.onload = initTable()
    </script>
</body>

</html>