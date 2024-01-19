<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data1 ? $data1['team']->getTeamName() : '-' }} vs {{ $data2 ? $data2['team']->getTeamName() : '-' }} |
        Stats |
        BULSCA
    </title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function addSuffix(i) {
            var a = i % 10,
                b = i % 100;

            if (i == 0) {
                return "0"
            }

            if (a == 1 && b != 11) {
                return i + "st";
            } else if (a == 2 && b != 12) {
                return i + "nd";
            } else if (a == 3 && b != 13) {
                return i + "rd";
            } else {
                return i + "th";
            }
        }
    </script>
</head>

<body class="overflow-x-hidden flex w-full h-full justify-center" x-data="{
    loader: false
}">
    <div class=" w-full md:w-[75%] m-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <a href="{{ route('public.results.stats.clubs') }}"
            class="link  items-center space-x-1  z-50 cursor-pointer">All Clubs</a>


        <div class="w-full flex justify-between bg-white rounded-b-md py-2 px-3 sticky top-0 left-0"
            x-data="{
            
                leftTeam: '{{ $data1 ? $data1['team']->getTeamSlug() : 'none.none' }}',
                rightTeam: '{{ $data2 ? $data2['team']->getTeamSlug() : 'none.none' }}',
                rawUrl: '{{ route('public.results.stats.compare', ['L', 'R']) }}',
            
                swapRightTeam(event) {
                    loader = true
            
                    targetUrl = this.rawUrl.replace('L', this.leftTeam).replace('R', event.target.value)
            
                    window.location = targetUrl
                },
            
                swapLeftTeam(event) {
                    loader = true
            
                    targetUrl = this.rawUrl.replace('L', event.target.value).replace('R', this.rightTeam)
            
                    window.location = targetUrl
                }
            }">
            <h1 class="font-bold  " style="font">
                <select name="" id="" style="min-width: 0 !important;" @change="swapLeftTeam">
                    <option value="none.none" class="text-base">Please select a team</option>
                    @foreach (App\Stats\Stats::getAllTeams() as $team)
                        <option value="{{ $team->name }}.{{ $team->team }}" class="text-base"
                            @if ($data1 && $team->name . '.' . $team->team == $data1['team']->getTeamSlug()) selected @endif>{{ $team->name }}
                            {{ $team->team }}</option>
                    @endforeach

                </select>
            </h1>
            <h1 class="font-bold text-bulsca_red text-3xl absolute bottom-0  left-[49.25%] ">vs</h1>
            <h1 class="font-bold  " style="font">


                <select name="" id="" class="text-right" @change="swapRightTeam">
                    <option value="none.none" class="text-base">Please select a team</option>
                    @foreach (App\Stats\Stats::getAllTeams() as $team)
                        <option value="{{ $team->name }}.{{ $team->team }}" class="text-base"
                            @if ($data2 && $team->name . '.' . $team->team == $data2['team']->getTeamSlug()) selected @endif>{{ $team->name }}
                            {{ $team->team }}</option>
                    @endforeach

                </select>
            </h1>
        </div>



        <div class="grid-2">
            @if ($data1 && $data2)
                @foreach ($stats as $stat)
                    @php
                        $stat->computeFor(['club' => $data1['strClub'], 'team' => $data1['strTeam']]);
                    @endphp
                    {{ $stat->render() }}
                    @php
                        $stat->computeFor(['club' => $data2['strClub'], 'team' => $data2['strTeam']]);
                    @endphp
                    {{ $stat->render() }}
                @endforeach
            @else
                <p class="w-full col-span-2 text-center font-semibold text-lg">Please select another team!</p>
            @endif

        </div>






    </div>



    </div>

    <div x-show="loader" x-transition style="display: none"
        class="fixed w-full h-full flex flex-col  items-center justify-center bg-white bg-opacity-90">
        <div class="text-center">
            <x-loader size=16 />
            <p class="text-bulsca text-lg font-semibold">Crunching the numbers...</p>
        </div>
    </div>


    <script>
        function setCommonMax(league) {
            let max = -1;

            document.body.querySelectorAll(`[x-league=${league}]`).forEach((c) => {
                let chart = Chart.getChart(c);

                let chartMax = chart.scales.y.max

                if (chartMax > max) {
                    max = chartMax
                }
            })

            document.body.querySelectorAll(`[x-league=${league}]`).forEach((c) => {
                let chart = Chart.getChart(c);

                chart.options.scales.y.max = max;
                chart.update()
            })


        }

        ['Overall', 'A', 'B'].forEach((league) => {
            setCommonMax(league)
        })
    </script>

</body>

</html>
