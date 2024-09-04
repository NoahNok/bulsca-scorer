<table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
        <tr>
            <th scope="col" class="py-3 px-6 text-left">
                Competitors(s)
            </th>
            <th scope="col" class="py-3 px-6">
                Points
            </th>
            <th scope="col" class="py-3 px-6">
                Position
            </th>


        </tr>
    </thead>
    <tbody>

        @forelse ($results['results'] as $region => $data)
            <tr class="bg-white border-b text-right ">

                <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $region }}
                </th>

                <td class="py-4 px-6">
                    {{ $data->score }}
                </td>

                <td class="py-4 px-6">
                    {{ $data->place }}
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
