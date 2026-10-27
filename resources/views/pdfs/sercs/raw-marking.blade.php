@extends('pdfs.layout')

@section('cover')
    <ul class="list-disc">
        @foreach ($serc->getJudges as $objective)
            <li>{{ $objective->name }}</li>
        @endforeach
    </ul>
@endsection

@section('content')
    {{-- One section per Objective; long tables carry on over pages with the header row repeated --}}
    @foreach ($serc->getJudges as $objective)
        <div class="pack-section">
            <h4 class="mb-2">{{ $objective->name }} | {{ $serc->getName() }}</h4>

            <table class="text-sm text-right w-full border">
                <thead class="text-xs uppercase bg-gray-50">
                    <tr>
                        <th class="py-2 px-2 border text-left whitespace-nowrap">Team</th>
                        @foreach ($objective->getMarkingPoints as $mp)
                            <th class="py-2 px-2 border text-left">{{ $mp->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($teams as $team)
                        <tr>
                            <td class="py-2 px-2 border text-left font-semibold whitespace-nowrap">{{ $team->getName($comp) }}</td>
                            @foreach ($objective->getMarkingPoints as $mp)
                                <td class="py-2 px-2 border"></td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
@endsection
