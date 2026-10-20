@extends('live.layout')

@section('content')
    <div class="flex flex-col gap-10" x-data="{
        search: $persist('').as('live-team-search'),
    
        matches(name) {
            const term = this.search.trim().toLowerCase()
            return term !== '' && String(name).toLowerCase().includes(term)
        },
    }">



        <section>
            <h2>SERC order</h2>
            <x-live.serc-order :comp="$comp" />
        </section>
    </div>
@endsection
