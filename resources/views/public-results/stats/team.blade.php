<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }} {{ $team }} | Stats | BULSCA</title>
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

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-full md:w-[75%] p-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <a href="{{ route('public.results.stats.clubs') }}"
            class="link flex items-center space-x-1  z-50 cursor-pointer">All Clubs</a>

        <div class="flex justify-between items-center" x-data="{
            compareUrl: '{{ route('public.results.stats.compare', ['L', 'R']) }}',
            leftTeam: '{{ $club->name . '.' . $team }}',
            rightTeam: 'none.none',
        
            compare() {
        
                let targetUrl = this.compareUrl.replace('L', this.leftTeam).replace('R', this.rightTeam)
        
                window.location = targetUrl
        
            }
        }">
            <h1 class="font-bold  " style="font">{{ $club->name }}
                {{ $team }}
            </h1>
            <p class="link" @click="compare">Compare against another team</p>
        </div>


        <div class="flex  ">Teams:
            @foreach ($teams as $t)
                <a href="{{ route('public.results.stats.club.team', [$club->name, $t->team]) }}"
                    class="link-stats px-2 first-of-type:ml-1 {{ $t->team == $team ? 'link-stats-selected' : '' }} ">
                    {{ $t->team }}
                </a>
            @endforeach
            <a href="{{ route('public.results.stats.club', [$club->name]) }}" class="link-stats  px-2  ">
                Unselect
            </a>
        </div>

        <br>
      
        <div class="grid grid-cols-6 gap-5" style="gap: 3rem 1.25rem">
            @foreach ($data as $d)
                {{ $d }}
            @endforeach
        </div>
        

    </div>



    </div>



</body>

</html>
