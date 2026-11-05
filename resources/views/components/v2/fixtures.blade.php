{{--
    The fixtures sheet (.v2-fixtures in v2.css): one table of competitions in the order given, past ones first,
    then a teal Today line with today's competitions on it, then upcoming ones. Every row is one whole-row link.

    past      competitions before today, in the order to show them
    live      today's competitions (shown on the Today line)
    upcoming  competitions after today
    today     draw the Today line (off for a sheet of past results only)
    types     badge each competition with its Championship type (Regional, Qualifier, Final)
    empty     what to say when there's nothing to show
--}}
@props([
    'past' => collect(),
    'live' => collect(),
    'upcoming' => collect(),
    'today' => true,
    'types' => false,
    'empty' => "No competitions yet. Organisers add them here as soon as they're set up.",
])

@php
    $typeName = fn($comp) => $types ? $comp->typeLabel() : null;
@endphp

@if ($past->isEmpty() && $live->isEmpty() && $upcoming->isEmpty())
    <p class="v2-fixtures-empty">{{ $empty }}</p>
@else
    <table class="v2-fixtures">
        <thead>
            <tr>
                <th>Date</th>
                <th>Competition</th>
                <th class="v2-fixtures-wide">Venue</th>
                <th class="v2-fixtures-wide"><span class="sr-only">Organisation</span></th>
                <th class="v2-fixtures-wide">Status</th>
                <th><span class="sr-only">Link</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($past as $comp)
                <tr class="is-past v2-row">
                    <td class="v2-fixtures-date">{{ $comp->when->format('j M') }}@if (!$comp->when->isCurrentYear())<span class="v2-fixtures-year">{{ $comp->when->format('Y') }}</span>@endif</td>
                    <td>
                        <span class="v2-fixtures-name">{{ $comp->name }}</span>
                        @if ($typeName($comp))
                            <span class="v2-badge">{{ $typeName($comp) }}</span>
                        @endif
                        <span class="v2-fixtures-where-sm">{{ $comp->where }}</span>
                    </td>
                    <td class="v2-fixtures-where v2-fixtures-wide">{{ $comp->where }}</td>
                    <td class="v2-fixtures-wide">
                        @if ($comp->getOrganisation?->getLogo())
                            <img src="{{ $comp->getOrganisation->getLogo() }}" alt="{{ $comp->getOrganisation->name }}">
                        @endif
                    </td>
                    <td class="v2-fixtures-wide">
                        @if (!$comp->public_results)
                            <span class="v2-fixtures-when">No results yet</span>
                        @elseif ($comp->areResultsProvisional())
                            <span class="v2-badge is-provisional">Provisional</span>
                        @else
                            <span class="v2-badge">Final</span>
                        @endif
                    </td>
                    <td>
                        @if ($comp->public_results)
                            <a href="{{ route('landing.competition.results', $comp->getSlug()) }}"
                                class="v2-link v2-row-link">Results<span class="sr-only"> for {{ $comp->name }}</span></a>
                        @else
                            <a href="{{ route('landing.competition', $comp->getSlug()) }}"
                                class="v2-link v2-row-link">Details<span class="sr-only"> for {{ $comp->name }}</span></a>
                        @endif
                    </td>
                </tr>
            @endforeach

            @if ($today)
                <tr class="v2-fixtures-today">
                    <td colspan="6">
                        <span class="v2-fixtures-rope"><span class="v2-fixtures-rope-label">Today,
                                {{ today()->format('D j M') }}</span></span>
                    </td>
                </tr>
            @endif

            @foreach ($live as $comp)
                <tr class="is-live v2-row">
                    {{-- The Today line above already gives the date, so the name takes its column --}}
                    <td colspan="2">
                        <span class="v2-fixtures-name">{{ $comp->name }}</span>
                        @if ($typeName($comp))
                            <span class="v2-badge">{{ $typeName($comp) }}</span>
                        @endif
                        <span class="v2-fixtures-where-sm">{{ $comp->where }}</span>
                    </td>
                    <td class="v2-fixtures-where v2-fixtures-wide">{{ $comp->where }}</td>
                    <td class="v2-fixtures-wide">
                        @if ($comp->getOrganisation?->getLogo())
                            <img src="{{ $comp->getOrganisation->getLogo() }}" alt="{{ $comp->getOrganisation->name }}">
                        @endif
                    </td>
                    <td class="v2-fixtures-wide">
                        @if ($comp->isLiveToday())
                            <span class="v2-fixtures-when is-live"><span class="v2-dot is-live" aria-hidden="true"></span>
                                Live now</span>
                        @else
                            <span class="v2-fixtures-when">Today</span>
                        @endif
                    </td>
                    <td>
                        <div class="v2-fixtures-actions">
                            <a href="{{ route('landing.competition', $comp->getSlug()) }}" class="v2-link v2-row-link"><span
                                    class="v2-fixtures-wide" aria-hidden="true">Details</span><span class="sr-only">Details for
                                    {{ $comp->name }}</span></a>
                            @if ($comp->isLiveToday())
                                <a href="{{ route('live', ['comp' => $comp->id]) }}"
                                    class="v2-btn v2-btn-primary v2-over-row">Follow live</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach

            @if ($today && $live->isNotEmpty() && $upcoming->isNotEmpty())
                <tr class="v2-fixtures-today">
                    <td colspan="6"><span class="v2-fixtures-rope" aria-hidden="true"></span></td>
                </tr>
            @endif

            @foreach ($upcoming as $comp)
                @php
                    $days = (int) today()->diffInDays($comp->when);
                @endphp
                <tr class="is-ahead v2-row">
                    <td class="v2-fixtures-date">{{ $comp->when->format('j M') }}@if (!$comp->when->isCurrentYear())<span class="v2-fixtures-year">{{ $comp->when->format('Y') }}</span>@endif</td>
                    <td>
                        <span class="v2-fixtures-name">{{ $comp->name }}</span>
                        @if ($typeName($comp))
                            <span class="v2-badge">{{ $typeName($comp) }}</span>
                        @endif
                        <span class="v2-fixtures-where-sm">{{ $comp->where }}</span>
                    </td>
                    <td class="v2-fixtures-where v2-fixtures-wide">{{ $comp->where }}</td>
                    <td class="v2-fixtures-wide">
                        @if ($comp->getOrganisation?->getLogo())
                            <img src="{{ $comp->getOrganisation->getLogo() }}" alt="{{ $comp->getOrganisation->name }}">
                        @endif
                    </td>
                    <td class="v2-fixtures-wide">
                        <span class="v2-fixtures-when">{{ $days == 1 ? 'Tomorrow' : "In $days days" }}</span>
                    </td>
                    <td><a href="{{ route('landing.competition', $comp->getSlug()) }}" class="v2-link v2-row-link">Details<span
                                class="sr-only"> for {{ $comp->name }}</span></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
