@extends('layouts.organisation')

@section('title')
    Officials
@endsection

@section('content')
    <div class="max-w-3xl">
        <h2 class="mb-0">Officials <span class="text-gray-400">({{ $officials->count() }})</span></h2>
        <p class="text-gray-500">Everyone who has officiated at one of {{ $org->name }}'s competitions.</p>
        <br>

        @if ($officials->isEmpty())
            <p class="text-gray-500">No one has officiated for {{ $org->name }} yet. Add officials from a competition's
                Officials tab.</p>
        @else
            <div class="se-card divide-y">
                @foreach ($officials as $official)
                    <div class="flex flex-col md:flex-row md:items-center gap-3 p-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate">{{ $official['name'] }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $official['email'] }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $official['count'] }} {{ Str::plural('competition', $official['count']) }}
                                · Last: {{ $official['last']['name'] }}@if ($official['last']['when']), {{ $official['last']['when']->format('M jS Y') }}@endif
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-1">
                            @foreach ($official['roles'] as $role)
                                <span class="badge badge-sm {{ $role === 'referee' ? 'badge-info' : 'badge-gray' }}">
                                    {{ strtoupper(App\Models\Competition::$officialRoles[$role] ?? $role) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
