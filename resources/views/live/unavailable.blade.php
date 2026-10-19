@extends('live.layout')

@section('title', 'Unavailable')

@section('content')
    @php
        $pastComps = \App\Models\Competition::where('when', '<=', now())
            ->where(function ($query) {
                $query->where('public_results', true)->orWhere('can_be_live', true);
            })
            ->orderBy('when', 'desc')
            ->paginate(9);
    @endphp

    <h2>Live</h2>
    <div class="se-alert-box alert-info mb-8">
        <p>{{ $message }}</p>
    </div>

    @if ($pastComps->isNotEmpty())
        <h3>Previously</h3>
        <div class="grid-3">
            @foreach ($pastComps as $pastComp)
                <x-competition-card url="{{ route('live') }}?comp={{ $pastComp->id }}" :comp="$pastComp" />
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pastComps->links() }}
        </div>
    @endif
@endsection
