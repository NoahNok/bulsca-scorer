<table>
    <thead>
        <tr>
            <th scope="col">
                Team
            </th>
            <th scope="col">
                Points
            </th>
            <th scope="col">
                Position
            </th>


        </tr>
    </thead>
    <tbody>

        @forelse ($results as $result)
            <tr>
                <th scope="row">
                    {{ $result->team }}
                </th>
                <td>
                    {{ round($result->totalPoints) }}
                </td>

                <td>
                    {{ $result->place }}
                </td>


            </tr>
        @empty
            <tr class="empty ">
                <th colspan="100" scope="row">
                    None
                </th>
            </tr>
        @endforelse



    </tbody>
</table>
