@extends('pdfs.layout')

@section('cover')
    <ol class="list-none">
        @foreach ($sercs as $serc)
            <li>{{ $tanks->sum(fn($tank) => count($tank)) }}x {{ $serc->getName() }} Marking Sheets (Over
                {{ count($tanks) }} tanks)</li>
        @endforeach
    </ol>
    <br>
    <p class="font-semibold">Pages with a black strip at the top mark the beginning of a new tank!</p>
@endsection

@section('content')
    {{-- One sheet per page: each goes to the judge marking that Draw --}}
    @foreach ($sercs as $serc)
        @php
            preg_match_all('/\b\w/', $serc->getName(), $matches);
            $sercShort = implode('', $matches[0]);

            // Embed the image so Chrome doesn't have to fetch it back from the site
            $image = $serc->image && Storage::disk('public')->exists($serc->image)
                ? 'data:' . Storage::disk('public')->mimeType($serc->image) . ';base64,' . base64_encode(Storage::disk('public')->get($serc->image))
                : null;
        @endphp

        @foreach ($tanks as $tankIndex => $tank)
            @foreach ($tank as $drawIndex => $draw)
                <div class="pack-section relative pt-4">
                    @if ($loop->first)
                        <div class="absolute top-0 left-0 w-full h-2 bg-black">&nbsp;</div>
                    @endif

                    <div class="flex w-full justify-between items-center">
                        <h2>{{ $serc->getName() }}</h2>
                        <p class="font-semibold">Initiative Judge</p>
                    </div>
                    <p>Tank {{ $draw->tank }} | Draw {{ $draw->draw }}</p>

                    <br>

                    <div class="flex justify-between items-center">
                        <h4>{{ $draw->entity?->getName($comp) ?? '-' }}</h4>
                        <p class="text-sm text-gray-500">{{ $draw->entity?->getLeague()?->name ?? 'No League' }}</p>
                    </div>

                    @if ($image)
                        <div class="flex items-center justify-center">
                            <img src="{{ $image }}" alt="SERC Image" class="w-[70%]">
                        </div>
                    @endif

                    <table class="table-fixed text-left w-full">
                        <thead class="border-b border-black">
                            <tr>
                                <th class="w-full">Criteria</th>
                                <th class="min-w-[5.5rem] text-right">Mark (/10)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($serc->getJudges as $judge)
                                <tr class="border-b border-black">
                                    <td class="py-1 bg-gray-200" colspan="2">{{ $judge->name }}
                                        <br>
                                        <article
                                            class="block prose prose-sm prose-neutral prose-p:mb-0 prose-ul:my-0 prose-ol:my-0 prose-li:my-0 leading-5!">
                                            {!! $judge->description !!}
                                        </article>
                                    </td>
                                </tr>
                                @foreach ($judge->getMarkingPoints as $mp)
                                    <tr class="border-b border-black">
                                        <td class="py-2 border-r border-black indent-3">{{ $mp->name }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <p class="mt-2"><strong>Comments:</strong></p>
                    <div class="h-40"></div>

                    <p><strong>Time:</strong><br><strong>Signature:</strong></p>

                    {{-- ROLE - EVENT - TANK - DRAW --}}
                    <p class="text-sm text-right">IJ - {{ $sercShort }} - T{{ $tankIndex + 1 }} - D{{ $drawIndex + 1 }}</p>
                </div>
            @endforeach
        @endforeach
    @endforeach
@endsection
