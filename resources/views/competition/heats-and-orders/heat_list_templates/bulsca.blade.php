@if ($heatEntries->count() == 0)
    <div class="empty">
        <a href="{{ route('comps.heats.gen', $comp) }}" class="se-btn">Generate Heats</a>
    </div>
@else
    <div class="se-table">
        <table>
            <thead>
                <tr>
                    <th scope="col">
                        Lane
                    </th>

                    @foreach ($heatEntries->sortBy(['heat', 'lane'])->groupBy('heat') as $key => $heat)
                        <th scope="col">
                            Heat {{ $key }}
                        </th>
                    @endforeach


                </tr>
            </thead>
            <tbody>
                @php
                    $tableEntries = $heatEntries->sortBy(['heat', 'lane'])->groupBy('heat');
                @endphp
                @for ($l = 1; $l <= $comp->max_lanes; $l++)
                    <tr>
                        <th scope="row">
                            {{ $l }}
                        </th>

                        @foreach ($tableEntries as $key => $heat)
                            @php
                                $lane = $heat->where('lane', $l)->first();
                            @endphp

                            <td>
                                @if ($lane)
                                    <span
                                        class="whitespace-nowrap text-ellipsis overflow-hidden">{{ $lane->club . ' ' . $lane->team }}</span>
                                @else
                                    &nbsp;
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
@endif
