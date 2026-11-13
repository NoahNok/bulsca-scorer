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
                            <th class="py-2 px-2 border">Raw mark</th>
                            <th class="py-2 px-2 border">Points</th>
                            <th class="py-2 px-2 border">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['results'] as $result)
                            <tr>
                                <td class="py-2 px-2 border text-left font-semibold">{{ $result->entry->name }}</td>
                                <td class="py-2 px-2 border">{{ $result->resolvedResult }}</td>
                                <td class="py-2 px-2 border">
                                    @if ($result->isDisqualified())
                                        <s>{{ round($result->points, 1) }}</s> DQ
                                    @else
                                        {{ round($result->points, 1) }}
                                    @endif
                                </td>
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
