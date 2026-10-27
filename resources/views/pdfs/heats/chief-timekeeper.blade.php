@extends('pdfs.layout')

@section('cover')
    <ol class="list-disc">
        @foreach ($sections as $section)
            <li>{{ count($section['heats']) }}x {{ $section['event']->getName() }} {{ $section['pool']?->name }} Heats</li>
        @endforeach
    </ol>
@endsection

@section('content')
    {{-- One section per event and Pool: heat numbers restart in each Pool --}}
    @foreach ($sections as $section)
        @php
            $pool = $section['pool'];
            $eventShort = \App\Pdf\CompetitionPdfCreator::initials($section['event']->getName());
            $poolShort = \App\Pdf\CompetitionPdfCreator::initials($pool?->name ?? '');
        @endphp

        <div class="pack-section">
            @foreach ($section['heats'] as $heatNo => $heat)
                <div class="pack-block {{ $onePerPage ? 'pack-page' : '' }} pb-8">
                    <h3 class="mb-2">{{ $section['event']->getName() }} · {{ \App\Models\Pool::heatLabel($pool, $heatNo) }}</h3>

                    <table class="table-fixed text-left w-full">
                        <thead class="border-b border-black">
                            <tr>
                                <th colspan="4"></th>
                                <th class="text-center" colspan="3">Time</th>
                            </tr>
                            <tr>
                                <th class="w-14 text-center">Lane</th>
                                <th class="w-40">Bracket</th>
                                <th>Name</th>
                                <th class="w-14 text-center">DQ</th>
                                <th class="w-14 text-center">Mins</th>
                                <th class="w-14 text-center">Secs</th>
                                <th class="w-14 text-center">100th</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($heat as $lane)
                                <tr class="border-b border-black">
                                    <td class="py-2 text-center">{{ $lane->lane }}</td>
                                    <td class="pr-4">{{ $lane->entity?->getLeague()?->name ?? '-' }}</td>
                                    <td>{{ $lane->entity?->getName($comp) }}</td>
                                    <td></td>
                                    <td class="border-x border-black"></td>
                                    <td class="border-x border-black"></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-sm" colspan="7"><strong>{{ count($heat) }}
                                        lane{{ count($heat) == 1 ? '' : 's' }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="flex items-end justify-between mt-4">
                        <div>
                            <p class="text-sm font-semibold">Order of finish</p>
                            <div class="border border-black divide-x divide-black flex items-center">
                                @foreach ($heat as $lane)
                                    <div class="size-10"></div>
                                @endforeach
                            </div>
                        </div>

                        <p class="text-right"><strong>Time:</strong> ____________<br><br><strong>Signature:</strong>
                            ____________</p>
                    </div>

                    {{-- ROLE - EVENT - HEAT - POOL --}}
                    <p class="text-sm text-right mt-2">CT - {{ $eventShort }} - H{{ $heatNo }}{{ $poolShort ? " - $poolShort" : '' }}</p>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
