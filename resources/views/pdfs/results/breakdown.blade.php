@extends('pdfs.layout')

@section('content')
    {{-- A section per Results sheet, each starting a new page --}}
    <div class="pack-sections">
        @forelse ($sections as $section)
            <div class="pack-section">
                <h4 class="mb-2">{{ $section['title'] }}</h4>

                <table class="text-sm text-right w-full border">
                    <thead class="text-xs uppercase bg-gray-50">
                        <tr>
                            <th class="py-2 px-2 border text-left">Entry</th>
                            @foreach ($section['results'][0]->events as $event)
                                <th class="py-2 px-2 border">{{ $event->eventName }}</th>
                            @endforeach
                            <th class="py-2 px-2 border">Total</th>
                            <th class="py-2 px-2 border">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['results'] as $result)
                            <tr>
                                <td class="py-2 px-2 border text-left font-semibold whitespace-nowrap">{{ $result->entry->name }}</td>
                                @foreach ($result->events as $event)
                                    <td class="py-2 px-2 border whitespace-nowrap">{{ round($event->adjustedPoints) }} ({{ $event->position }})</td>
                                @endforeach
                                <td class="py-2 px-2 border font-semibold">{{ round($result->totalPoints) }}</td>
                                <td class="py-2 px-2 border">{{ $result->position }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @include('pdfs.results.rounded')
            </div>
        @empty
            @include('pdfs.results.empty')
        @endforelse
    </div>
@endsection
