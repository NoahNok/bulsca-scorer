@extends('layouts.admin')

@section('title')
    Users | Admin
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
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <h2 class="mb-0">Users <span class="text-gray-400">({{ $users->total() }})</span></h2>

        <form action="{{ route('admin.users.index') }}" method="get" class="flex items-center gap-2">
            <div class="relative flex-1 md:w-80">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true"
                    class="size-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="search" name="q" value="{{ $q }}" aria-label="Search users"
                    placeholder="Search by name or email" class="w-full border-2 rounded-md py-2 px-4 pl-10! bg-white">
            </div>
            <button type="submit" class="btn">Search</button>
            @if ($q !== '')
                <a href="{{ route('admin.users.index') }}" class="link text-sm">Clear</a>
            @endif
        </form>
    </div>
    <br>

    @if ($users->isEmpty())
        <p class="text-gray-500">
            @if ($q !== '')
                No users match “{{ $q }}”.
            @else
                No users yet.
            @endif
        </p>
    @else
        <div class="se-table se-table-thin">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" class="text-left!">Email</th>
                        <th scope="col" class="text-left!">Type</th>
                        <th scope="col">Competitions</th>
                        <th scope="col">Officiating</th>
                        <th scope="col">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <th class="relative">
                                {{ $user->name }}
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="absolute top-0 left-0 w-full h-full" aria-label="View {{ $user->name }}"></a>
                            </th>
                            <td class="text-left">{{ $user->email }}</td>
                            <td class="text-left whitespace-nowrap">
                                @if ($user->isJudgeOnly())
                                    <span class="badge badge-orange badge-sm">OFFICIAL ONLY</span>
                                @else
                                    <span class="badge badge-info badge-sm">FULL ACCOUNT</span>
                                @endif
                                @if ($user->isAdmin())
                                    <span class="badge badge-danger badge-sm">ADMIN</span>
                                @endif
                            </td>
                            <td>{{ $user->competitions_count }}</td>
                            <td>{{ $user->officiating_count }}</td>
                            <td class="whitespace-nowrap">{{ $user->created_at?->format('M jS Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br>
        {{ $users->links() }}
    @endif
@endsection
