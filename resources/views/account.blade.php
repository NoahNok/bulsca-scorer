@extends('layouts.guest')

@section('title', 'Your account')

@section('content')
    <div class="max-w-xl">
        <h2 class="mb-0">Your account</h2>
        <p class="text-gray-500">{{ $user->email }}</p>
        <br>

        <h3>Details</h3>
        <form action="{{ route('account.update') }}" method="post" class="flex items-end gap-2">
            @csrf
            <x-form-input id="name" title="Name" :defaultValue="$user->name" extraCss="flex-1" />
            <button type="submit" class="btn mb-1">Save</button>
        </form>
        <br>

        <h3>Signed-in devices</h3>
        <div class="se-card divide-y">
            @foreach ($devices as $device)
                <div class="flex items-center gap-3 p-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">
                            {{ $device['name'] }}
                            @if ($device['current'])
                                <span class="ml-1 badge badge-info badge-sm">THIS DEVICE</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $device['browser'] ?: 'Unknown browser' }}@if ($device['ip'])
                                &middot; {{ $device['ip'] }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $device['current'] ? 'Active now' : 'Last active ' . $device['lastActive'] }}@if ($device['signedIn'])
                                &middot; signed in {{ $device['signedIn'] }}
                            @endif
                        </p>
                    </div>
                    @unless ($device['current'])
                        <form action="{{ route('account.devices.destroy', $device['id']) }}" method="post"
                            onsubmit="return confirm(@js('Sign out ' . $device['name'] . '? It will need to sign in again.'))">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-small">Sign out</button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>

        @if (count($devices) > 1)
            <form action="{{ route('account.devices.destroy-others') }}" method="post" class="mt-3"
                onsubmit="return confirm('Sign out every device except this one?')">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger">Sign out all other devices</button>
            </form>
        @endif
    </div>
@endsection
