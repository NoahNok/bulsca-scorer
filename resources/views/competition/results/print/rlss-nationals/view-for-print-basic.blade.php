<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        @if ($comp->areResultsProvisional())
            (PROVISIONAL)
        @endif{{ $schema->name }} | {{ $comp->name }}
    </title>
</head>
<div class="   ">

    <div class="w-screen h-48 bg-rlss-blue flex  items-center justify-start px-6 overflow-x-hidden space-x-6"
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="">
            <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
            <p class=" font-ariel text-rlss-yellow font-semibold">{{ $comp->where }}</p>
        </div>

        <div class="w-1 h-12  border-r border-white"></div>

        <div class="">
            <h3 class=" text-white font-astoria hmb-0">{{ $schema->name }}</h3>
        </div>

        <div class="!ml-auto   ">
            <img src="{{ $comp->getBrand->getLogo() }}" class=" w-20 h-20" alt="">
        </div>


    </div>
    <table class=" text-sm w-full overflow-hidden text-left text-gray-500 ">
        <thead class="text-xs  text-right  bg-gray-50 text-rlss-blue">
            <tr class="border border-rlss-blue font-bold">
                <th class="bg-rlss-blue border-x border-rlss-blue w-8"></th>
                <th scope="col" class="py-2 px-4 text-left bg-rlss-blue bg-opacity-40 border-x border-rlss-blue ">
                    Competitors
                </th>
                <th scope="col" class="py-2 px-4 border-x border-rlss-blue">
                    Points
                </th>
                <th scope="col" class="py-2 px-4 border-x border-rlss-blue">
                    Position
                </th>


            </tr>
        </thead>
        <tbody>

            @forelse ($results['results'] as $result)
                <tr class="bg-white border-b border-rlss-blue  text-right text-rlss-blue ">
                    <td
                        class="py-3 text-center text-xs whitespace-nowrap bg-rlss-blue text-white border-x  border-rlss-blue">
                        {{ $result->place }}
                    </td>
                    <td scope="row"
                        class="py-3 text-left text-xs px-4 whitespace-nowrap font-bold bg-rlss-blue bg-opacity-40  border-x border-rlss-blue">
                        {{ $result->name }}
                    </td>

                    <td class="py-3 px-4 border-x text-xs border-rlss-blue">
                        {{ round($result->score) }}
                    </td>

                    <td class="py-3 px-4 border-x text-xs border-rlss-blue">
                        {{ $result->place }}
                    </td>


                </tr>
            @empty
                <tr class="bg-white border-b text-right ">
                    <th colspan="100" scope="row"
                        class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                        None
                    </th>
                </tr>
            @endforelse



        </tbody>
    </table>

</div>


<script>
    window.onload = function() {
        window.print()
    }
</script>
