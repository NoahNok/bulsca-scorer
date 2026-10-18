@extends('layouts.organisation')

@section('title')
    Officials
@endsection

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between gap-2">
            <h2 class="mb-0">Officials <span class="text-gray-400">({{ $total }})</span></h2>
            @if ($total > 0)
                <a href="{{ route('orgs.officials.export', [$org, 'q' => $q ?: null, 'role' => $role ?: null]) }}"
                    class="se-btn">Export CSV</a>
            @endif
        </div>
        <p class="text-gray-500">Everyone who has officiated at one of {{ $org->name }}'s competitions.</p>
        <br>

        @if ($total === 0)
            <p class="text-gray-500">No one has officiated for {{ $org->name }} yet. Add officials from a competition's
                Officials tab.</p>
        @else
            <form action="{{ route('orgs.officials', $org) }}" method="get"
                class="flex flex-col md:flex-row md:items-center gap-2 mb-4">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true"
                        class="size-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="search" name="q" value="{{ $q }}" aria-label="Search officials"
                        placeholder="Search by name or email" class="w-full border-2 rounded-md py-2 px-4 pl-10! bg-white">
                </div>
                <div class="flex items-center gap-2">
                    <select name="role" aria-label="Filter by role" onchange="this.form.submit()"
                        class="border-2 rounded-md py-2 px-2 bg-white">
                        <option value="">All roles</option>
                        @foreach (App\Models\Competition::$officialRoles as $value => $roleName)
                            <option value="{{ $value }}" @selected($role === $value)>{{ $roleName }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="se-btn">Search</button>
                    @if ($q !== '' || $role !== '')
                        <a href="{{ route('orgs.officials', $org) }}" class="link text-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($officials->isEmpty())
                <p class="text-gray-500">No officials match your search.</p>
            @else
                @if ($q !== '' || $role !== '')
                    <p class="text-sm text-gray-500 mb-2">Showing {{ $officials->count() }} of {{ $total }}</p>
                @endif

                <div class="se-card divide-y">
                    @foreach ($officials as $official)
                        <div class="flex flex-col md:flex-row md:items-center gap-3 p-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold truncate">{{ $official['name'] }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $official['email'] }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $official['count'] }} {{ Str::plural('competition', $official['count']) }}
                                    · Last: <a href="{{ route('comps.officials', $official['last']['id']) }}"
                                        class="link">{{ $official['last']['name'] }}</a>@if ($official['last']['when']), {{ $official['last']['when']->format('M jS Y') }}@endif
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1">
                                @foreach ($official['roles'] as $officialRole)
                                    <span
                                        class="badge badge-sm {{ $officialRole === 'referee' ? 'badge-info' : 'badge-gray' }}">
                                        {{ strtoupper(App\Models\Competition::$officialRoles[$officialRole] ?? $officialRole) }}
                                    </span>
                                @endforeach
                            </div>

                            @if ($upcomingCompetitions->isNotEmpty())
                                <button type="button" class="se-btn shrink-0"
                                    @click="$dispatch('add-to-competition', @js(['id' => $official['id'], 'name' => $official['name']])); modals.addToCompetition = true">
                                    Add to a competition
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    @if ($upcomingCompetitions->isNotEmpty())
        <x-s-e-modal id="addToCompetition" title="Add to a competition" :open="$errors->addToCompetition->any()">
            <form action="{{ route('orgs.officials.add', $org) }}" id="addToCompetitionForm" method="post"
                x-data="{ official: @js($oldOfficial ? ['id' => $oldOfficial['id'], 'name' => $oldOfficial['name']] : null) }"
                @add-to-competition.window="official = $event.detail">
                @csrf
                <input type="hidden" name="user_id" :value="official?.id">

                <p class="mb-4">Adding <strong x-text="official?.name"></strong>. We'll email them to let them know.</p>
                @error('user_id', 'addToCompetition')
                    <div class="se-alert-box mb-4">
                        <p>{{ $message }}</p>
                    </div>
                @enderror

                <div class="se-form-input @error('competition_id', 'addToCompetition') is-invalid @enderror">
                    <label for="add-to-competition">Competition</label>
                    <select name="competition_id" id="add-to-competition">
                        @foreach ($upcomingCompetitions as $upcoming)
                            <option value="{{ $upcoming->id }}" @selected((int) old('competition_id') === $upcoming->id)>
                                {{ $upcoming->name }}@if ($upcoming->when), {{ $upcoming->when->format('M jS Y') }}@endif
                            </option>
                        @endforeach
                    </select>
                    @error('competition_id', 'addToCompetition')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                <div class="se-form-input imb-0 @error('role', 'addToCompetition') is-invalid @enderror">
                    <label for="add-to-competition-role">Role</label>
                    <select name="role" id="add-to-competition-role">
                        @foreach (App\Models\Competition::$officialRoles as $value => $roleName)
                            <option value="{{ $value }}" @selected(old('role', 'official') === $value)>{{ $roleName }}</option>
                        @endforeach
                    </select>
                    @error('role', 'addToCompetition')
                        <small>{{ $message }}</small>
                    @enderror
                </div>
            </form>

            <x-slot name="footer">
                <button type="submit" form="addToCompetitionForm" class="se-btn se-btn-success">Add</button>
            </x-slot>
        </x-s-e-modal>
    @endif
@endsection
