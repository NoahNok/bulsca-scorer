{{--
    A competition's Results tab. Up to three tabs (Overall: Master sheets; Results sheets; Events), each only when
    something in it is published (LandingController::showResults). A row of chips picks the sheet, or the Event; Results
    sheets and Leagues are chips, or a select when there are more than three and on phones; the table loads from the
    results endpoints. The tab and picks live in the url, so a link opens the same table. Violations and penalties open
    in a modal.
--}}
@extends('landing.competition.layout')

@section('title', 'Results | ' . $comp->name)

@php
    $config = [
        'tabs' => $tabs->keys(),
        'groups' => $groups,
        'leagues' => $leagues,
        'leagueFirst' => (bool) $comp->select_league_first,
        'urls' => [
            'overall' => route('landing.competition.results.get.master.sheet', [$comp->getSlug(), 'schema' => '_id']),
            'sheets' => route('landing.competition.results.get.sheet', [$comp->getSlug(), 'schema' => '_id']),
            'events' => route('landing.competition.results.get', [$comp->getSlug(), 'league' => '_league', 'event' => '_id', 'type' => '_type']),
            'violation' => route('landing.competition.results.get.violation', [$comp->getSlug(), 'violation_id' => '_id', 'violation_type' => '_type']),
            'breakdown' => route('landing.competition.results.breakdown.serc', [$comp->getSlug(), 'serc' => '_id']),
        ],
    ];
    $first = $tabs->keys()->first();
@endphp

@section('comp-content')
    @if ($tabs->isEmpty())
        <p class="v2-empty">Results appear here once an Organiser publishes them.</p>
    @else
        @if ($comp->areResultsProvisional())
            <div class="v2-alert is-warning mb-6" role="note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                </svg>
                <div><strong>Provisional</strong>These results are provisional and may still change.</div>
            </div>
        @endif

        <div x-data="publicResults(@js($config))">
            @if ($tabs->count() > 1)
                <div class="v2-tabs v2-results-tabs" role="tablist" aria-label="Results"
                    style="--v2-tabs: {{ $tabs->count() }}">
                    <span class="v2-tabs-pill" aria-hidden="true"
                        :style="`transform: translateX(${tabs.indexOf(tab) * 100}%)`"></span>
                    @foreach ($tabs as $key => $label)
                        <button type="button" role="tab" @click="tab = '{{ $key }}'"
                            :aria-selected="(tab === '{{ $key }}').toString()"
                            aria-selected="{{ $key === $first ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            @endif

            {{-- Eases the height as chips show and hide and tables swap, instead of the page jumping --}}
            <div x-smooth-height>
                <div>
                    {{-- Picking: a sheet in Overall and Results sheets; a League, then an Event, in Events --}}
                    <div class="v2-results-picks">
                        @foreach (['overall' => 'Master sheet', 'sheets' => 'Results sheet'] as $key => $label)
                            @if ($tabs->has($key) && $groups[$key]->count() > 1)
                                <div x-show="tab === '{{ $key }}'" @if ($key !== $first) x-cloak @endif>
                                    @if ($key === 'sheets')
                                        {{-- Chips for up to three, or a select when there are more, and always on phones --}}
                                        <div @class(['v2-field v2-results-select', 'is-phone-only' => $groups[$key]->count() <= 3])>
                                            <label for="results-sheet" class="sr-only">{{ $label }}</label>
                                            <select id="results-sheet" class="v2-input" :value="picks.sheets"
                                                @change="pick('sheets', $event.target.value)">
                                                @foreach ($groups[$key] as $sheet)
                                                    <option value="{{ $sheet['id'] }}">{{ $sheet['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if ($key !== 'sheets' || $groups[$key]->count() <= 3)
                                        <div @class(['v2-results-chips', 'v2-results-select-chips' => $key === 'sheets']) role="group" aria-label="{{ $label }}">
                                            @foreach ($groups[$key] as $sheet)
                                                <button type="button" class="v2-chip" @click="pick('{{ $key }}', {{ $sheet['id'] }})"
                                                    :aria-pressed="(picks.{{ $key }} == {{ $sheet['id'] }}).toString()">{{ $sheet['name'] }}</button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        @if ($tabs->has('events'))
                            <div x-show="tab === 'events'" @if ($first !== 'events') x-cloak @endif>
                                @if ($leagues->count() > 1)
                                    {{-- Chips for up to three, or a select when there are more, and always on phones --}}
                                    <div @class(['v2-field v2-results-select', 'is-phone-only' => $leagues->count() <= 3])>
                                        <label for="results-league" class="sr-only">League</label>
                                        <select id="results-league" class="v2-input" :value="league ?? ''"
                                            @change="league = $event.target.value">
                                            @if ($comp->select_league_first)
                                                <option value="" disabled>Choose a League</option>
                                            @endif
                                            @foreach ($leagues as $league)
                                                <option value="{{ $league['id'] }}">{{ $league['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($leagues->count() <= 3)
                                        <div class="v2-results-chips v2-results-select-chips" role="group" aria-label="League">
                                            @foreach ($leagues as $league)
                                                <button type="button" class="v2-chip" @click="league = @js($league['id'])"
                                                    :aria-pressed="(league == @js($league['id'])).toString()">{{ $league['name'] }}</button>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                                <div @class(['v2-results-chips', 'is-under-leagues' => $leagues->count() > 1]) role="group" aria-label="Event">
                                    @foreach ($groups['events'] as $event)
                                        <button type="button" class="v2-chip" @click="pick('events', @js($event['key']))"
                                            :aria-pressed="(picks.events === @js($event['key'])).toString()">{{ $event['name'] }}
                                            @if ($event['type'] === 'serc')
                                                <span class="v2-badge">SERC</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="v2-results-head" x-show="current" x-cloak>
                        <h2 x-text="current?.name"></h2>
                        <div class="flex flex-wrap items-center gap-4">
                            {{-- A Results sheet's columns per event: its place and points in each --}}
                            <label class="v2-check" x-show="tab === 'sheets' && hasDetail">
                                <input type="checkbox" x-model="detailed"> Show each event
                            </label>
                            <a class="v2-link" x-show="tab === 'events' && current?.type === 'serc' && league !== null && !error"
                                :href="breakdownUrl()">Breakdown</a>
                        </div>
                    </div>

                    @if ($comp->select_league_first && $tabs->has('events'))
                        <p class="v2-empty" x-show="tab === 'events' && league === null" @if ($first !== 'events') x-cloak @endif>
                            Choose a League to see the results.</p>
                    @endif

                    {{-- Only before the first table: after that the last one stays, dimmed, until the next arrives --}}
                    <p class="v2-results-status" x-show="loading && !table" x-cloak role="status">Loading results&hellip;</p>
                    <div class="v2-alert is-warning" x-show="error" x-cloak role="alert">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v5M12 16h.01" />
                        </svg>
                        <div><strong>Results unavailable</strong><span x-text="error"></span></div>
                    </div>

                    <div class="v2-table-scroll" x-show="table" x-cloak x-ref="table" :class="loading && 'is-loading'"
                        :aria-busy="loading.toString()">
                        <table class="v2-table">
                            <thead>
                                <tr>
                                    <template x-for="column in visibleColumns" :key="column.id">
                                        <th scope="col" x-text="column.label"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, r) in table?.rows ?? []" :key="r">
                                    <tr :class="`is-place-${row.position?.data}`">
                                        <template x-for="(column, c) in visibleColumns" :key="column.id">
                                            <td :class="c === 0 && 'v2-table-name'">
                                                <template x-if="row[column.id].type === 'string'">
                                                    <span x-text="row[column.id].data ?? '-'"></span>
                                                </template>
                                                <template x-if="row[column.id].type === 'string-array'">
                                                    <div>
                                                        <template x-for="item in row[column.id].data">
                                                            <div x-text="item"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row[column.id].type === 'violation'">
                                                    <div class="v2-results-violations">
                                                        <template x-for="violation in row[column.id].data">
                                                            <button type="button" class="v2-link-inline" x-text="violation.display"
                                                                @click="openViolation(violation, row.name?.data)"></button>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row[column.id].type === 'violation-combined'">
                                                    <div>
                                                        <template x-for="item in row[column.id].data">
                                                            <div class="v2-results-violations">
                                                                <template x-if="typeof item === 'string'">
                                                                    <span x-text="item"></span>
                                                                </template>
                                                                <template x-for="violation in (typeof item === 'string' ? [] : item)">
                                                                    <button type="button" class="v2-link-inline" x-text="violation.display"
                                                                        @click="openViolation(violation)"></button>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row[column.id].type === 'result'">
                                                    <div>
                                                        <span x-text="row[column.id].data.is"></span>
                                                        <template x-if="row[column.id].data.is != row[column.id].data.was">
                                                            <small class="v2-results-was">Was <span x-text="row[column.id].data.was"></span></small>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row[column.id].type === 'result-combined'">
                                                    <div class="v2-results-combined">
                                                        <div>
                                                            <template x-for="time in row[column.id].data.combined.results">
                                                                <div x-text="time"></div>
                                                            </template>
                                                        </div>
                                                        <span x-text="row[column.id].data.is"></span>
                                                    </div>
                                                </template>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <p class="v2-results-status" x-show="table && table.rows.length === 0">No results in this yet.</p>
                    </div>
                </div>
            </div>

            <div class="v2-modal-backdrop" x-show="modal" x-cloak x-transition.opacity @click.self="modal = false"
                @keydown.escape.window="modal = false">
                <div class="v2-modal" role="dialog" aria-modal="true" aria-labelledby="violation-title"
                    x-trap.noscroll="modal">
                    <h3 id="violation-title" x-text="violation?.code"></h3>
                    <p class="v2-results-for" x-show="violation?.for" x-text="violation?.for"></p>
                    <p x-text="violation?.description"></p>
                    <template x-if="violation?.submission">
                        <dl class="v2-details mt-4">
                            <dt>Reported by</dt>
                            <dd x-text="`${violation.submission.name} (${violation.submission.position})`"></dd>
                            <dt>Seconded by</dt>
                            <dd x-text="`${violation.submission.seconder_name} (${violation.submission.seconder_position})`"></dd>
                            <dt>Turn and length</dt>
                            <dd x-text="`Turn ${violation.submission.turn ?? '-'}, length ${violation.submission.length ?? '-'}`"></dd>
                            <dt>Details</dt>
                            <dd x-text="violation.submission.details || 'No details given.'"></dd>
                        </dl>
                    </template>
                    <div class="v2-modal-actions">
                        <button type="button" class="v2-btn" @click="modal = false">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function publicResults(config) {
                const first = (key) => config.groups[key][0] ?? null;

                return {
                    ...config,
                    tab: config.tabs[0],
                    // The pick in each tab: a sheet's id, or an Event's "type-id" key
                    picks: {
                        overall: first('overall')?.id ?? null,
                        sheets: first('sheets')?.id ?? null,
                        events: first('events')?.key ?? null,
                    },
                    // "All Leagues" to start, unless the competition asks for a League first
                    league: config.leagueFirst ? null : 'all',
                    detailed: false,
                    table: null,
                    loading: false,
                    error: null,
                    modal: false,
                    violation: null,
                    request: 0,

                    get current() {
                        const id = this.picks[this.tab];
                        return this.groups[this.tab]?.find((item) => (this.tab === 'events' ? item.key : item.id) == id) ?? null;
                    },

                    get hasDetail() {
                        return (this.table?.columns ?? []).some((column) => column.detail);
                    },

                    get visibleColumns() {
                        return (this.table?.columns ?? []).filter((column) => !column.detail || this.detailed);
                    },

                    init() {
                        const params = new URLSearchParams(window.location.search);
                        if (this.tabs.includes(params.get('tab'))) this.tab = params.get('tab');
                        if (params.has('sheet') && this.tab !== 'events') {
                            const sheet = this.groups[this.tab].find((item) => item.id == params.get('sheet'));
                            if (sheet) this.picks[this.tab] = sheet.id;
                        }
                        if (params.has('event') && this.groups.events.some((item) => item.key === params.get('event'))) {
                            this.picks.events = params.get('event');
                        }
                        if (params.has('league')) {
                            const league = this.leagues.find((item) => item.id == params.get('league'));
                            if (league) this.league = league.id;
                        }
                        this.detailed = params.get('display') === 'detailed';

                        this.$watch('tab', () => this.load());
                        this.$watch('league', () => this.load());
                        this.$watch('detailed', () => this.remember());
                        this.load();
                    },

                    pick(tab, id) {
                        this.picks[tab] = id;
                        this.load();
                    },

                    // Keep the view in the url, so a link opens the same table
                    remember() {
                        const url = new URL(window.location);
                        ['tab', 'sheet', 'event', 'league', 'display'].forEach((key) => url.searchParams.delete(key));
                        if (this.tab !== this.tabs[0]) url.searchParams.set('tab', this.tab);
                        if (this.tab === 'events') {
                            if (this.picks.events) url.searchParams.set('event', this.picks.events);
                            // All Leagues is the default, so it's left out, unless the competition makes you choose
                            if (this.league !== null && (this.league !== 'all' || this.leagueFirst)) url.searchParams.set('league', this.league);
                        } else if (this.picks[this.tab]) {
                            url.searchParams.set('sheet', this.picks[this.tab]);
                        }
                        if (this.tab === 'sheets' && this.detailed) url.searchParams.set('display', 'detailed');
                        window.history.replaceState({}, '', url);
                    },

                    async load() {
                        this.remember();
                        this.error = null;

                        const item = this.current;
                        if (!item || (this.tab === 'events' && this.league === null)) {
                            this.request++;
                            this.table = null;
                            this.loading = false;
                            return;
                        }

                        const url = this.tab === 'events'
                            ? this.urls.events.replace('_league', this.league).replace('_id', item.id).replace('_type', item.type)
                            : this.urls[this.tab].replace('_id', item.id);

                        // A slow answer for an earlier pick mustn't replace a later one
                        const request = ++this.request;
                        this.loading = true;
                        try {
                            const response = await fetch(url, { headers: { Accept: 'application/json' } });
                            const json = await response.json();
                            if (request !== this.request) return;
                            if (response.ok) {
                                this.table = this.tab === 'events' ? this.fromEvent(json) : this.fromSheet(json);
                                this.$nextTick(() => this.fadeIn());
                            } else {
                                this.table = null;
                                this.error = json.error || 'Try again in a minute.';
                            }
                        } catch {
                            if (request === this.request) {
                                this.table = null;
                                this.error = 'Check your connection and try again.';
                            }
                        } finally {
                            if (request === this.request) this.loading = false;
                        }
                    },

                    // The new table fades in over the dimmed one it replaces
                    fadeIn() {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        this.$refs.table?.animate([{ opacity: 0.4 }, { opacity: 1 }], { duration: 200, easing: 'ease-out' });
                    },

                    // An Event's table: columns starting "_" are for the page, and a cell is a value or {type, data}
                    fromEvent(json) {
                        const columns = Object.entries(json.columns)
                            .filter(([id]) => !id.startsWith('_'))
                            .map(([id, label]) => ({ id, label, detail: false }));
                        const cell = (value) => value !== null && typeof value === 'object' ? value : { type: 'string', data: value };

                        return {
                            columns,
                            rows: json.data.map((row) => Object.fromEntries(columns.map((column) => [column.id, cell(row.data[column.id])]))),
                        };
                    },

                    // A sheet's table: "event:type" columns hold a place and points, shown with "Show each event"
                    fromSheet(json) {
                        const columns = Object.entries(json.columns)
                            .map(([id, label]) => ({ id, label, detail: id.includes(':') }));
                        const cell = (value) => ({
                            type: 'string',
                            data: value !== null && typeof value === 'object' ? `${value.position} (${value.points} pts)` : value,
                        });

                        return {
                            columns,
                            rows: json.data.map((row) => Object.fromEntries(columns.map((column) => [column.id, cell(row[column.id])]))),
                        };
                    },

                    breakdownUrl() {
                        const url = this.urls.breakdown.replace('_id', this.current?.id);
                        return this.league !== null ? `${url}?league=${this.league}` : url;
                    },

                    async openViolation(violation, name = '') {
                        const response = await fetch(this.urls.violation.replace('_id', violation.id).replace('_type', violation.type));
                        if (!response.ok) return;
                        const data = await response.json();
                        this.violation = { ...data, for: data.for || name };
                        this.modal = true;
                    },
                };
            }
        </script>
    @endif
@endsection
