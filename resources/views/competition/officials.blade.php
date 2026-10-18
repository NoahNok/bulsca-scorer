@extends('layouts.competition')

@section('title')
    Officials
@endsection

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between gap-2">
            <h2 class="mb-0">Officials <span class="text-gray-400">({{ $officials->count() }})</span></h2>
            <button type="button" class="se-btn se-btn-success" @click="modals.addOfficial = true">Add Official</button>
        </div>
        <p class="text-gray-500">
            People who can judge this competition on Digital Judge. Officials can also add themselves
            @if ($comp->digitalJudgeEnabled)
                with the Digital Judge PIN <strong>{{ $comp->digitalJudgePin }}</strong>, or by scanning the
                <a href="{{ route('dj.qrs', $comp) }}" target="_blank" class="link">printable QR code</a>.
            @else
                with the Digital Judge PIN once Digital Judge is enabled.
            @endif
        </p>
        <br>

        @unless ($hasReferee)
            <div class="se-alert-box mb-4">
                <h1>No referee yet</h1>
                <p>Only referees can review DQs and penalties, release SERCs and confirm results on Digital Judge.
                    Give at least one official the Referee role.</p>
            </div>
        @endunless

        <h3>Current officials</h3>
        @if ($officials->isEmpty())
            <p class="text-gray-500">No officials yet. Add one, or share the Digital Judge PIN.</p>
        @else
            <div class="se-card divide-y">
                @foreach ($officials as $official)
                    @php
                        if ($official['neverSignedIn']) {
                            $seen = "Hasn't signed in yet";
                        } elseif (!$official['lastSeen']) {
                            $seen = "Hasn't opened it on Digital Judge yet";
                        } elseif (!$official['online']) {
                            $seen = 'Last seen ' . $official['lastSeen']->diffForHumans();
                        } else {
                            $seen = null;
                        }
                    @endphp
                    <div class="flex flex-col md:flex-row md:items-center gap-3 p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">
                                {{ $official['name'] }}
                                @if ($official['role'] === 'referee')
                                    <span class="ml-1 badge badge-info badge-sm">REFEREE</span>
                                @endif
                                @if ($official['online'])
                                    <span class="ml-1 badge badge-success badge-sm">ONLINE</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 truncate">{{ $official['email'] }}</p>
                            @if ($official['joined'] || $seen)
                                <p class="text-xs text-gray-400">
                                    {{ collect([
                                        $official['joined'] ? 'Added ' . $official['joined']->format('M jS Y') : null,
                                        $seen,
                                    ])->filter()->implode(' · ') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @unless ($official['lastSeen'])
                                <form action="{{ route('comps.officials.resend', [$comp, $official['id']]) }}" method="post">
                                    @csrf
                                    <button type="submit" class="se-btn">Resend email</button>
                                </form>
                            @endunless

                            <form action="{{ route('comps.officials.update', [$comp, $official['id']]) }}" method="post">
                                @csrf
                                <select name="role" class="border-2 rounded-md py-1 px-2 bg-white text-sm"
                                    aria-label="Role for {{ $official['name'] }}"
                                    @if ($official['onlyReferee'])
                                        @change="if ($el.value !== 'referee' && !await askConfirm(@js('Make ' . $official['name'] . ' an official? They\'re the only referee, so no one will be able to review DQs and penalties or confirm results.'))) { $el.value = 'referee'; return } $el.form.submit()"
                                    @else
                                        onchange="this.form.submit()"
                                    @endif>
                                    @foreach (App\Models\Competition::$officialRoles as $role => $roleName)
                                        <option value="{{ $role }}" @selected($official['role'] === $role)>
                                            {{ $roleName }}</option>
                                    @endforeach
                                </select>
                            </form>

                            <form action="{{ route('comps.officials.destroy', [$comp, $official['id']]) }}" method="post"
                                @submit="doConfirm($event, @js($official['removeMessage']))">
                                @csrf
                                @method('delete')
                                <button type="submit" class="se-btn se-btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <x-s-e-modal id="addOfficial" title="Add Official" :open="$errors->addOfficial->any() || $errors->copyOfficials->any()">
        <form action="{{ route('comps.officials.store', $comp) }}" id="addOfficialForm" method="post" x-data="{
            query: @js(old('email', '')),
            results: [],
            exactEmail: false,
            searched: '',
            selected: @js(old('user_id') ? ['id' => (int) old('user_id'), 'name' => old('selected_name'), 'email' => old('selected_email')] : null),
            // people from the organisation's other competitions, shown before anything's typed
            suggestions: @js($suggestions),
        
            get isEmail() {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.query.trim())
            },
        
            // the name box is only for making a new account
            get isNewAccount() {
                return !this.selected && this.isEmail && this.searched === this.query.trim() && !this.exactEmail
            },
        
            search() {
                const q = this.query.trim()
                if (q.length < 2) {
                    this.results = []
                    this.searched = ''
                    return
                }
        
                fetch('{{ route('comps.officials.search', $comp) }}?q=' + encodeURIComponent(q))
                    .then(resp => resp.json())
                    .then(data => {
                        if (q !== this.query.trim()) return
                        this.results = data.results
                        this.exactEmail = data.exactEmail
                        this.searched = q
                    })
            },
        
            select(account) {
                if (account.official) return
                this.selected = account
                this.results = []
            },
        
            clear() {
                this.selected = null
                this.$nextTick(() => this.$refs.search.focus())
            }
        }" x-init="if (query) search()">
            @csrf

            <p class="text-sm mb-4">They'll get an email and see the competition on their Digital Judge home page.</p>

            <div class="se-form-input relative @error('email', 'addOfficial') is-invalid @enderror">
                <label for="official-search">Official</label>

                <template x-if="selected">
                    <div class="flex items-center gap-3 border-b-2 border-b-gray-300 py-2 px-4 bg-white">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate" x-text="selected.name"></p>
                            <p class="text-sm text-gray-500 truncate" x-text="selected.email"></p>
                        </div>
                        <button type="button" class="se-btn" @click="clear">Change</button>
                        <input type="hidden" name="user_id" :value="selected.id">
                        <input type="hidden" name="selected_name" :value="selected.name">
                        <input type="hidden" name="selected_email" :value="selected.email">
                    </div>
                </template>

                <div class="relative" x-show="!selected">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true"
                        class="size-5 absolute left-3 top-5 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="email" id="official-search" x-ref="search" autocomplete="off"
                        placeholder="Search by name or email" class="w-full pl-10! mb-0!" x-model="query"
                        :disabled="!!selected" @input.debounce.250ms="search" @keydown.escape="results = []"
                        @click.outside="results = []">

                    <div class="absolute w-full top-11 left-0 z-10 bg-white border rounded-md shadow-sm overflow-hidden"
                        x-show="results.length > 0" x-cloak>
                        <template x-for="account in results" :key="account.id">
                            <button type="button" @click="select(account)" :disabled="account.official"
                                class="w-full text-left flex items-center justify-between gap-3 px-3 py-2 text-sm border-b last:border-b-0"
                                :class="account.official ? 'text-gray-400 cursor-not-allowed' : 'hover:bg-gray-100'">
                                <span class="truncate">
                                    <span class="font-semibold" x-text="account.name"></span>
                                    <span class="text-gray-500" x-text="account.email"></span>
                                </span>
                                <span class="text-xs shrink-0" x-show="account.official">Already an official</span>
                            </button>
                        </template>
                    </div>

                    <div class="mt-2" x-show="query.trim() === '' && suggestions.length > 0" x-cloak>
                        <p class="text-sm text-gray-500 mb-1">From your other competitions</p>
                        <div class="border rounded-md overflow-hidden bg-white">
                            <template x-for="account in suggestions" :key="account.id">
                                <button type="button" @click="select(account)"
                                    class="w-full text-left flex items-center justify-between gap-3 px-3 py-2 text-sm border-b last:border-b-0 hover:bg-gray-100">
                                    <span class="truncate">
                                        <span class="font-semibold" x-text="account.name"></span>
                                        <span class="text-gray-500" x-text="account.email"></span>
                                    </span>
                                    <span class="text-xs text-gray-400 truncate shrink-0 max-w-[40%]" x-text="'Last: ' + account.last"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 mt-1" x-show="!isEmail && searched && searched === query.trim() && results.length === 0" x-cloak>
                        No one found. To add someone new, enter their email address.
                    </p>
                </div>

                @error('email', 'addOfficial')
                    <small>{{ $message }}</small>
                @enderror
                @error('user_id', 'addOfficial')
                    <small>{{ $message }}</small>
                @enderror
            </div>

            <template x-if="isNewAccount">
                <div class="se-form-input @error('name', 'addOfficial') is-invalid @enderror">
                    <label for="official-name">Name</label>
                    <input type="text" name="name" id="official-name" value="{{ old('name') }}" autocomplete="off"
                        class="mb-0!" required>
                    <small class="info">No account uses this email yet, so we'll make one. They sign in
                        with a PIN we email them.</small>
                    @error('name', 'addOfficial')
                        <small>{{ $message }}</small>
                    @enderror
                </div>
            </template>

            <div class="se-form-input imb-0 @error('role', 'addOfficial') is-invalid @enderror">
                <label for="official-role">Role</label>
                <select name="role" id="official-role">
                    @foreach (App\Models\Competition::$officialRoles as $role => $roleName)
                        <option value="{{ $role }}" @selected(old('role', 'official') === $role)>{{ $roleName }}</option>
                    @endforeach
                </select>
                @error('role', 'addOfficial')
                    <small>{{ $message }}</small>
                @enderror
            </div>
        </form>

        @if ($previousCompetitions->isNotEmpty())
            <form action="{{ route('comps.officials.copy', $comp) }}" method="post" class="border-t pt-4 mt-4">
                @csrf
                <div class="se-form-input imb-0 @error('from', 'copyOfficials') is-invalid @enderror">
                    <label for="copy-from">Or add everyone from another competition</label>
                    <div class="flex items-center gap-2">
                        <select name="from" id="copy-from" class="flex-1 min-w-0">
                            @foreach ($previousCompetitions as $previous)
                                <option value="{{ $previous->id }}" @selected((int) old('from') === $previous->id)>
                                    {{ $previous->name }}@if ($previous->when), {{ $previous->when->format('M jS Y') }}@endif
                                    · {{ $previous->officials_count }} {{ Str::plural('official', $previous->officials_count) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="se-btn shrink-0">Add all</button>
                    </div>
                    <small class="info">They keep the role they had there, and we email anyone who's new.</small>
                    @error('from', 'copyOfficials')
                        <small>{{ $message }}</small>
                    @enderror
                </div>
            </form>
        @endif

        <x-slot name="footer">
            <button type="submit" form="addOfficialForm" class="se-btn se-btn-success">Add</button>
        </x-slot>
    </x-s-e-modal>

@endsection
