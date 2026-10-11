@extends('layouts.admin')

@section('title')
    {{ $user->name }} | Users | Admin
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.users.index') }}">Users</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl">
        <h2 class="mb-0">
            {{ $user->name }}
            @if ($user->isJudgeOnly())
                <span class="ml-1 badge badge-orange badge-sm align-middle">OFFICIAL ONLY</span>
            @else
                <span class="ml-1 badge badge-info badge-sm align-middle">FULL ACCOUNT</span>
            @endif
            @if ($user->isAdmin())
                <span class="badge badge-danger badge-sm align-middle">ADMIN</span>
            @endif
        </h2>
        <p class="text-gray-500 break-all">{{ $user->email }}</p>
        <p class="text-sm text-gray-400">
            {{ collect([
                $user->created_at ? 'Joined ' . $user->created_at->format('M jS Y') : null,
                $neverSignedIn ? "Hasn't signed in yet" : ($user->email_verified_at ? 'Verified ' . $user->email_verified_at->format('M jS Y') : 'Email not verified'),
                $lastActive ? 'Last active ' . $lastActive->diffForHumans() : null,
            ])->filter()->implode(' · ') }}
        </p>
        <br>

        <h3>Competitions <span class="text-gray-400">({{ $competitions->count() }})</span></h3>
        @if ($competitions->isEmpty())
            <p class="text-gray-500">No access to any competitions.</p>
        @else
            <div class="se-card divide-y">
                @foreach ($competitions as $comp)
                    <div class="flex flex-col md:flex-row md:items-center gap-2 p-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.comp.view', $comp['id']) }}"
                                class="font-semibold truncate hover:underline">{{ $comp['name'] }}</a>
                            <p class="text-xs text-gray-400">
                                {{ collect([$comp['when']?->format('M jS Y')])
                                    ->concat(collect($comp['via'])->map(fn ($access, $org) => "Via {$org} ({$access})"))
                                    ->filter()->implode(' · ') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-1 md:justify-end">
                            @foreach ($comp['access'] as $access)
                                <span class="badge badge-gray badge-sm">{{ $access }}</span>
                            @endforeach
                            @if ($comp['via'])
                                <span class="badge badge-info badge-sm">ORGANISATION</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <br>

        <h3>Organisations <span class="text-gray-400">({{ $organisations->count() }})</span></h3>
        @if ($organisations->isEmpty())
            <p class="text-gray-500">Not a member of any organisations.</p>
        @else
            <div class="se-card divide-y">
                @foreach ($organisations as $org)
                    <div class="flex flex-col md:flex-row md:items-center gap-2 p-3">
                        <p class="flex-1 min-w-0 font-semibold truncate">{{ $org['name'] }}</p>
                        <div class="flex flex-wrap gap-1 md:justify-end">
                            @foreach ($org['access'] as $access)
                                <span class="badge badge-gray badge-sm">{{ $access }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <br>

        <h3>Officiating <span class="text-gray-400">({{ $officiating->count() }})</span></h3>
        @if ($officiating->isEmpty())
            <p class="text-gray-500">Not an official at any competitions.</p>
        @else
            <div class="se-card divide-y">
                @foreach ($officiating as $comp)
                    <div class="flex flex-col md:flex-row md:items-center gap-2 p-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.comp.view', $comp['id']) }}"
                                class="font-semibold truncate hover:underline">{{ $comp['name'] }}</a>
                            <p class="text-xs text-gray-400">
                                {{ collect([
                                    $comp['when']?->format('M jS Y'),
                                    $comp['added'] ? 'Added ' . $comp['added']->format('M jS Y') : null,
                                ])->filter()->implode(' · ') }}
                            </p>
                        </div>
                        <span
                            class="badge {{ $comp['role'] === 'referee' ? 'badge-info' : 'badge-gray' }} badge-sm self-start md:self-auto">{{ strtoupper($comp['roleName']) }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($submissions?->total > 0)
            <br>
            <h3>Judge submissions <span class="text-gray-400">({{ $submissions->total }})</span></h3>
            <div class="grid-3">
                <div class="se-card se-card-body">
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold">{{ (int) $submissions->pending }}</p>
                </div>
                <div class="se-card se-card-body">
                    <p class="text-sm text-gray-500">Accepted</p>
                    <p class="text-2xl font-semibold">{{ (int) $submissions->accepted }}</p>
                </div>
                <div class="se-card se-card-body">
                    <p class="text-sm text-gray-500">Rejected</p>
                    <p class="text-2xl font-semibold">{{ (int) $submissions->rejected }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
