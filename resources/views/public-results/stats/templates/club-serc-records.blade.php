<div class="card">
    <h3>SERC Records</h3>
    <div class="w-full h-full overflow-x-auto">
        <table class="table-auto min-w-full">
            <thead class="text-left">
                <tr>
                    <th class="px-2 pl-0">SERC</th>
                    <th class="px-2">Points</th>
                    <th class="px-2">Team</th>
                    <th class="px-2">Competition</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($data as $serc)
                    <tr>
                        @php

                        @endphp
                        <td class="px-2 pl-0"><a
                                href="{{ route('public.results.serc', [$serc->comp_name . '.' . $serc->comp_id, $serc->serc_id]) }}"
                                class="link">{{ $serc->serc_name }}</a>
                        </td>
                        <td class="px-2 whitespace-nowrap">
                            {{ round($serc->total) }}/{{ round($serc->max) }}
                            (<strong>{{ round(($serc->total / $serc->max) * 100, 2) }}%</strong>)
                        </td>
                        <td class="px-2">{{ $serc->team }}</td>
                        <td class="px-2"><a
                                href="{{ route('public.results.comp', $serc->comp_name . '.' . $serc->comp_id) }}"
                                class="link  whitespace-nowrap">
                                {{ $serc->comp_name }}
                            </a></li>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>