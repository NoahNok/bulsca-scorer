@extends('live.layout')

@section('title', $event->getName() . ' DQs & Penalties | ' . $comp->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('live.dqs', request()->all()) }}" class="text-sm text-gray-600 hover:text-se transition-colors">&larr;
            All events</a>
        <h2>{{ $event->getName() }} · DQs & Penalties</h2>
    </div>

    <div class="flex flex-col gap-3">
        @forelse ($dqs as $dq)
            @php
                $heat = $dq->getHeat;
            @endphp

            <div class="se-card" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="se-card-body flex items-center justify-between gap-3 w-full text-left cursor-pointer">
                    <span class="flex flex-wrap items-center gap-3">
                        <span class="font-semibold">{{ $heat?->entity?->getName($comp) ?? '-' }}</span>
                        <span class="badge badge-danger badge-sm">{{ $dq->code ?? '-' }}</span>
                        @if ($dq->appealed)
                            <span class="badge badge-info badge-sm">Appealed</span>
                        @endif
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div x-show="open" x-collapse x-cloak>
                    <div class="se-card-body border-t flex flex-col gap-4">
                        <div>
                            <p class="font-semibold text-gray-900!">{{ $dq->code ?? '-' }}</p>
                            <p>{{ $descriptions[$dq->id] }}</p>
                        </div>

                        <div class="flex flex-col md:flex-row md:gap-8 gap-1">
                            <p><strong>Heat</strong>: {{ $heat?->heat ?? '-' }}</p>
                            <p><strong>Lane</strong>: {{ $heat?->lane ?? '-' }}</p>
                            <p><strong>Turn</strong>: {{ $dq->turn ?? '-' }}</p>
                            <p><strong>Length</strong>: {{ $dq->length ?? '-' }}</p>
                        </div>

                        <div class="flex flex-col md:flex-row md:gap-8 gap-1">
                            <p><strong>Reporter</strong>: {{ $dq->name ?? '-' }} ({{ $dq->position ?? '-' }})</p>
                            <p><strong>Seconder</strong>: {{ $dq->seconder_name ?? '-' }} ({{ $dq->seconder_position ?? '-' }})</p>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900!">Additional judge details</p>
                            <p>{{ $dq->details ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="se-alert-box alert-info">
                <p>No DQs or penalties for this event 🎉</p>
            </div>
        @endforelse
    </div>
@endsection
