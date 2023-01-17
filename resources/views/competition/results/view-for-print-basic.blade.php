<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        {{ $schema->name }} | {{ $comp->name }}
    </title>
</head>
<div class="   ">
    <h2>{{ $schema->name }} | {{ $comp->name }}</h2>
    <table class=" text-sm  rounded-lg overflow-hidden text-left text-gray-500 ">
        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
            <tr>
                <th scope="col" class="py-2 px-4 text-left">
                    Team
                </th>
                <th scope="col" class="py-2 px-4">
                    Points
                </th>
                <th scope="col" class="py-2 px-4">
                    Position
                </th>


            </tr>
        </thead>
        <tbody>

            @forelse ($results as $result)
            <tr class="bg-white border-b text-right ">
                <th scope="row" class="py-2 text-left px-4 font-medium text-black whitespace-nowrap ">
                    {{ $result->team }}
                </th>
                <td class="py-2 px-4 text-black">
                    {{ round($result->totalPoints) }}
                </td>

                <td class="py-2 px-4 text-black">
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


<script>
    window.onload = function() {
        window.print()
    }
</script>