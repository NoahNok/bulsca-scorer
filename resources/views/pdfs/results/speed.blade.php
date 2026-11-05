@extends('pdfs.layout')

@section('content')
    {{-- Overall, then each League with results, each starting a new page --}}
    <div class="pack-sections">
        @forelse ($sections as $section)
            <div class="pack-section">
                <h4 class="mb-2">{{ $section['title'] }}</h4>

                <table class="text-sm text-right w-full border">
                    <thead class="text-xs uppercase bg-gray-50">
                        <tr>
                            <th class="py-2 px-2 border text-left">Entry</th>
                            <th class="py-2 px-2 border">{{ $event->getName() == 'Rope Throw' ? 'Ropes/Time' : 'Time' }}</th>
                            <th class="py-2 px-2 border">Points</th>
                            <th class="py-2 px-2 border">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['results'] as $result)
                            <tr>
                                @if ($result->isCombined())
                                    {{-- A combined result: each member's time beside the time that counts --}}
                                    <td class="py-2 px-2 border text-left font-semibold">
                                        @foreach ($result->combined as $item)
                                            <div>{{ $item->entity->getName($comp) }}</div>
                                        @endforeach
                                    </td>
                                    <td class="py-2 px-2 border">
                                        <div class="flex justify-end items-center gap-2">
                                            <div class="border-r pr-2">
                                                @foreach ($result->combined as $item)
                                                    <div>{{ App\Models\SpeedResult::prettyTime($item->result) }}</div>
                                                @endforeach
                                            </div>
                                            <div>{{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}</div>
                                        </div>
                                    </td>
                                @else
                                    <td class="py-2 px-2 border text-left font-semibold">{{ $result->entity->getName($comp) }}</td>
                                    <td class="py-2 px-2 border">
                                        {{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}
                                        @if ($result->resolvedResult != $result->result)
                                            <div class="text-xs text-gray-500">Was {{ App\Models\SpeedResult::prettyTime($result->result) }}</div>
                                        @endif
                                    </td>
                                @endif
                                <td class="py-2 px-2 border">{{ $result->isDisqualified() && !$showDqPoints ? 'DQ' : (round($result->points, 1) ?: '-') }}</td>
                                <td class="py-2 px-2 border">{{ $result->position }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            @include('pdfs.results.empty')
        @endforelse
    </div>
@endsection
