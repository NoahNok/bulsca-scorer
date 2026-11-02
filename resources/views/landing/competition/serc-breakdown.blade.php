{{--
    A SERC's breakdown, under the competition's Results tab: every Objective's marking points for every Team, sortable,
    with the Officials' overall feedback, each Team's notes and Violations in modals, a filter, an analysis view that
    colours each mark against the rest, and full screen.
--}}
@extends('landing.competition.layout')

@section('title', $event->getName() . ' breakdown | ' . $comp->name)

@section('v2-meta')
    <script src="{{ asset('js/sorttable.js') }}?{{ config('version.hash') }}"></script>
@endsection

@php
    $back = route('landing.competition.results', [
        $comp->getSlug(),
        'tab' => 'events',
        'event' => 'serc-' . $event->id,
        ...(request('league') ? ['league' => request('league')] : []),
    ]);
@endphp

@section('comp-content')
    <div x-data="{
        dqOpen: false,
        notesOpen: false,
        dqUrl: '',
        dqBaseUrl: '{{ route('public.results.dq-pen', [$comp->id, 'T', 'C']) }}',
        notesBaseUrl: '{{ route('landing.competition.results.notes.serc', [$comp->id, $event->id, 'T']) }}',
        notes: null,

        showDqPen(code, teamId) {
            if (!code) return;
            this.dqUrl = this.dqBaseUrl.replace('T', teamId).replace('C', code);
            this.dqOpen = true;
        },
        async loadNotes(teamId) {
            this.notes = null;
            this.notesOpen = true;
            const response = await fetch(this.notesBaseUrl.replace('T', teamId));
            this.notes = response.ok ? await response.json() : { name: '', notes: [] };
        },
    }">
        <a href="{{ $back }}" class="v2-link-muted">&larr; Back to results</a>
        <h2 class="v2-results-head-title">{{ $event->getName() }} breakdown</h2>

        @if ($comp->areResultsProvisional())
            <div class="v2-alert is-warning mt-4" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                </svg>
                <div><strong>Provisional</strong>These results are provisional and may still change.</div>
            </div>
        @endif

        @if (count($overallJudgeNotes) > 0)
            <details class="v2-breakdown-feedback" open>
                <summary>Overall feedback</summary>
                <div>
                    @foreach ($overallJudgeNotes as $note)
                        <div>
                            <strong>{{ $note->judge }}</strong>
                            <p>{{ $note->note }}</p>
                        </div>
                    @endforeach
                </div>
            </details>
        @endif

        <div class="v2-breakdown-tools">
            <div class="v2-field">
                <label for="team-filter" class="sr-only">Filter Teams</label>
                <input id="team-filter" type="search" class="v2-input" placeholder="Filter Teams">
                <p class="v2-hint">A name, <code>team:a</code> or <code>league:a</code></p>
            </div>
            <label class="v2-check"><input type="checkbox" id="anal"> Analysis</label>
            <button type="button" class="v2-btn v2-btn-sm" id="fs">Full screen</button>
        </div>

        <div id="table-wrapper" class="v2-table-scroll v2-breakdown">
            <button type="button" class="v2-btn v2-btn-sm hidden" id="cfs">Close full screen</button>
            <table id="table" class="v2-table" sortable>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        @foreach ($fsd['judges'] as $judge => $mps)
                            <th colspan="{{ count($mps) + 1 }}" class="is-judge">{{ $judge }}</th>
                        @endforeach
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr data-sortable-row>
                        <th scope="col" data-sortable>Team</th>
                        <th scope="col">Notes</th>
                        @foreach ($fsd['judges'] as $judge => $mpIds)
                            @foreach ($mpIds as $markingPoint)
                                <th scope="col" @class(['is-point', 'is-judge-start' => $loop->first])
                                    title="{{ $markingPoint['name'] }}" data-sortable>
                                    <span>{{ $markingPoint['name'] }}</span>
                                </th>
                            @endforeach
                            <th scope="col" class="is-point is-judge-total" data-sortable><span>Total</span></th>
                        @endforeach
                        <th scope="col">DQ</th>
                        <th scope="col">Raw mark</th>
                        <th scope="col" data-sortable>Points</th>
                        <th scope="col" data-sortable>Position</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        @foreach ($fsd['judges'] as $judge => $mpIds)
                            @foreach ($mpIds as $markingPoint)
                                <th @class(['is-weight', 'is-judge-start' => $loop->first])>{{ $markingPoint['weight'] }}</th>
                            @endforeach
                            <th class="is-weight is-judge-total">-</th>
                        @endforeach
                        <th></th>
                        <th>Max {{ round($event->getMaxMark(), 1) }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse ($fsd['results'] as $tid => $team)
                        <tr class="place-{{ $team['place'] }}">
                            <th scope="row">{{ $team['team'] }}</th>
                            <td><button type="button" class="v2-link" @click="loadNotes({{ $team['tid'] }})">Notes</button></td>
                            @foreach ($fsd['judges'] as $judge => $mpIds)
                                @php
                                    $localTotal = 0;
                                @endphp
                                @foreach ($mpIds as $mpId => $markingPoint)
                                    @php
                                        $localTotal += ($team['results'][$mpId] ?? 0) * $markingPoint['weight'];
                                    @endphp
                                    <td @class(['is-mark', 'is-judge-start' => $loop->first])>{{ round($team['results'][$mpId] ?? 0) }}</td>
                                @endforeach
                                <td class="is-mark is-judge-total" data-total-row>{{ $localTotal }}</td>
                            @endforeach
                            <td>
                                @if ($team['disqualification'])
                                    <button type="button" class="v2-link-inline"
                                        @click="showDqPen('{{ $team['disqualification'] }}', {{ $team['tid'] }})">{{ $team['disqualification'] }}</button>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="font-semibold">{{ round($team['raw']) }}</td>
                            <td class="font-bold">{{ round($team['points']) }}</td>
                            <td>{{ $team['place'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100">No results in this SERC yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (!$comp->areResultsProvisional())
            <p class="v2-breakdown-more">
                <a href="{{ route('whatif') }}" class="v2-link-inline">Edit in WhatIf</a> to see how changes would
                affect the overall results, or <a href="{{ Request::url() }}?dlCSV" class="v2-link-inline" data-no-prefetch>download it as
                    a CSV</a>.
            </p>
        @endif

        <div class="v2-modal-backdrop" x-show="dqOpen" x-cloak x-transition.opacity @click.self="dqOpen = false"
            @keydown.escape.window="dqOpen = false">
            <div class="v2-modal v2-breakdown-dq" role="dialog" aria-modal="true" aria-label="Violation"
                x-trap.noscroll="dqOpen">
                <iframe :src="dqUrl" title="Violation" class="w-full"
                    onload="this.height=this.contentWindow.document.body.scrollHeight;"></iframe>
                <div class="v2-modal-actions">
                    <button type="button" class="v2-btn" @click="dqOpen = false">Close</button>
                </div>
            </div>
        </div>

        <div class="v2-modal-backdrop" x-show="notesOpen" x-cloak x-transition.opacity @click.self="notesOpen = false"
            @keydown.escape.window="notesOpen = false">
            <div class="v2-modal" role="dialog" aria-modal="true" aria-labelledby="notes-title"
                x-trap.noscroll="notesOpen">
                <h3 id="notes-title">Notes<span x-show="notes?.name" x-text="` for ${notes?.name}`"></span></h3>
                <p x-show="!notes">Loading&hellip;</p>
                <div class="v2-breakdown-notes" x-show="notes">
                    <template x-for="note in notes?.notes ?? []">
                        <div>
                            <strong x-text="note.judge"></strong>
                            <p x-text="note.note"></p>
                        </div>
                    </template>
                    <p x-show="notes && !notes.notes.length">No notes were given for this Team.</p>
                </div>
                <div class="v2-modal-actions">
                    <button type="button" class="v2-btn" @click="notesOpen = false">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/vendor/chart.js@4.5.1/chart.umd.min.js"></script>
    <script src="{{ asset('js/analysis.js') }}"></script>
    <script>
        (function() {
            const table = document.getElementById('table');
            const tableBody = document.getElementById('table-body');
            const tableRows = tableBody.querySelectorAll('tr');
            const tableWrapper = document.getElementById('table-wrapper');
            const fs = document.getElementById('fs');
            const cfs = document.getElementById('cfs');

            fs.onclick = () => {
                tableWrapper.requestFullscreen();
                cfs.classList.remove('hidden');
            };
            cfs.onclick = () => document.exitFullscreen();
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) cfs.classList.add('hidden');
            });

            analyze(tableRows);
            document.getElementById('anal').onchange = () => tableBody.classList.toggle('analysis');

            // Light up the column under the pointer
            const column = (e, on) => {
                if (e.target.nodeName !== 'TD') return;
                const index = Array.from(e.target.parentNode.children).indexOf(e.target);
                tableBody.querySelectorAll('tr').forEach(tr => tr.children[index]?.classList.toggle('is-col', on));
            };
            table.onmouseover = (e) => column(e, true);
            table.onmouseout = (e) => column(e, false);

            // "team:a" matches a Team letter, "league:a" A teams or the rest, anything else a name
            document.getElementById('team-filter').oninput = (e) => {
                const term = e.target.value.toLowerCase();
                tableRows.forEach(row => {
                    const name = row.children[0].textContent.trim().toLowerCase();
                    let hide;
                    if (term.startsWith('team:')) {
                        hide = !name.endsWith(term.substr(5));
                    } else if (term.startsWith('league:')) {
                        hide = term.substr(7, 1) === 'a' ? !name.endsWith('a') : name.endsWith('a');
                    } else {
                        hide = !name.includes(term);
                    }
                    row.hidden = hide;
                });
            };

            Sorttable();
        })();
    </script>
@endsection
