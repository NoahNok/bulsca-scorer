<div class="card">
    <h3>Speed Records</h3>
    <div class="w-full h-full overflow-x-auto">
        <table class="table-auto min-w-full">
            <thead class="text-left">
                <tr class="gap-1">
                    <th class="px-2 pl-0">Event</th>
                    <th class="px-2">Time</th>
                    <th class="px-2">Team</th>
                    <th class="px-2">Competition</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($data as $record)
                    <tr class=" space" x-speed-event="{{ $record['se'] }}" x-speed-record="{{ $record['result'] }}">

                        <td class="px-2 pl-0 whitespace-nowrap">{{ $record['se'] }}</td>
                        <td class="px-2">
                            {{ $record['result'] == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record['result']) }}
                        </td>
                        <td class="px-2">{{ $record['team'] ?? '-' }}</td>
                        <td class="px-2"><a class="link"
                                href="{{ route('public.results.comp', ($record['comp_name'] ?? '') . '.' . ($record['comp_id'] ?? '')) }}">{{ $record['comp_name'] ?? '-' }}</a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>