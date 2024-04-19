<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data1 ? $team1 : '-' }} vs {{ $data2 ? $team2 : '-' }} |
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


        <div class="w-full flex md:flex-row flex-col md:justify-between bg-white rounded-b-md py-2 px-3 sticky top-0 left-0 z-50"
            x-data="{
            
                leftTeam: '{{ $data1 ? $team1Slug : 'none.none' }}',
                rightTeam: '{{ $data2 ? $team2Slug : 'none.none' }}',
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
            <h1 class="font-bold  text-3xl md:text-5xl " style="min-width: 0px !important;">
                <select name="" id="" class="w-full" style="min-width: 0 !important;" @change="swapLeftTeam">
                    <option value="none.none" class="text-base">Please select a team</option>
                    @foreach (App\Stats\StatsManager::getAllTeams() as $team)
                        <option value="{{ $team->name }}.{{ $team->team }}" class="text-base"
                            @if ($data1 && $team->name . '.' . $team->team == $team1Slug) selected @endif>{{ $team->name }}
                            {{ $team->team }}</option>
                    @endforeach

                </select>
            </h1>
            <h1
                class="font-bold text-bulsca_red text-xl md:text-3xl absolute md:bottom-4 bottom-[35%]  left-5 md:left-[49.25%] ">
                vs
            </h1>
            <h1 class="font-bold text-3xl md:text-5xl   " style="min-width: 0px !important;">


                <select name="" id="" class="md:text-right w-full" @change="swapRightTeam">
                    <option value="none.none" class="text-base">Please select a team</option>
                    @foreach (App\Stats\StatsManager::getAllTeams() as $team)
                        <option value="{{ $team->name }}.{{ $team->team }}" class="text-base"
                            @if ($data2 && $team->name . '.' . $team->team == $team2Slug) selected @endif>{{ $team->name }}
                            {{ $team->team }}</option>
                    @endforeach

                </select>
            </h1>
        </div>

        <div class="md:hidden mb-2 font-semibold">
            <p class="indent-4">Swipe left/right to move between teams</p>
        </div>


        <div class="overflow-auto max-w-screen snap-x snap-mandatory ">
            <div class="grid grid-cols-2  gap-4 min-w-full w-[200%] md:w-full " >
                @if ($data1 && $data2)
                    <div class="flex flex-col space-y-4 snap-center">
                        @foreach ($data1 as $d1)
                            {{ $d1 }}
                        @endforeach
                    </div>
                    <div class="flex flex-col space-y-4 snap-center">
                        @foreach ($data2 as $d2)
                            {{ $d2 }}
                        @endforeach
                    </div>
                @else
                    <p class="w-full col-span-2 text-center font-semibold text-lg">Please select another team!</p>
                @endif
    
            </div>
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

        function compareSpeedTimes() {
            let allEvents = []
            document.body.querySelectorAll('[x-speed-event]').forEach((el) => {

                let eventName = el.getAttribute('x-speed-event')

                if (!allEvents.includes(eventName)) {
                    allEvents.push(eventName)
                }

            })

            allEvents.forEach(event => {
                let fasterElement = null;

                document.body.querySelectorAll(`[x-speed-event='${event}']`).forEach((el) => {
                    if (!fasterElement) {
                        fasterElement = el
                    } else {
                        let fasterTime = parseInt(fasterElement.getAttribute('x-speed-record'))
                        let thisTime = parseInt(el.getAttribute('x-speed-record'))

                        if (thisTime < fasterTime) {
                            fasterElement = el
                        }
                    }
                })

                fasterElement.classList.add('bg-green-100')

            })
        }
        compareSpeedTimes()

        document.querySelectorAll('.absolute').forEach((el) => {
            el.style.backgroundColor = 'transparent'
        });



    </script>

</body>

</html>
