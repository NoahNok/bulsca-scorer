@extends('pdfs.layout')

@section('content')
    @if (count($results) == 0)
        @include('pdfs.results.empty')
    @else
        <table class="text-sm text-right w-full border">
            <thead class="text-xs uppercase bg-gray-50">
                <tr>
                    <th class="py-2 px-2 border text-left">Entry</th>
                    <th class="py-2 px-2 border">Points</th>
                    <th class="py-2 px-2 border">Position</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr>
                        <td class="py-2 px-2 border text-left font-semibold">{{ $result->entity->getName($comp) }}</td>
                        <td class="py-2 px-2 border">{{ round($result->totalPoints) }}</td>
                        <td class="py-2 px-2 border">{{ $result->position }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @include('pdfs.results.rounded')
    @endif
@endsection
