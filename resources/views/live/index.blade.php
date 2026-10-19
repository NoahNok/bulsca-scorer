@extends('live.layout')

@section('content')
    <div class="flex flex-col gap-10" x-data="{
        search: $persist('').as('live-team-search'),

        matches(name) {
            const term = this.search.trim().toLowerCase()
            return term !== '' && String(name).toLowerCase().includes(term)
        },
    }">

        <div class="se-form-input mb-0! md:w-1/2">
            <label for="team-search">Find your team</label>
            <input id="team-search" type="text" x-model="search" placeholder="Team or club name..." class="mb-0!"
                autocomplete="off">
            <small class="info">Highlights your place in the SERC order.</small>
        </div>

        <section>
            <h2>SERC order</h2>
            <x-live.serc-order :comp="$comp" searchable />
        </section>
    </div>
@endsection
